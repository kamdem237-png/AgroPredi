import argparse
import json
import os
import time
from pathlib import Path

import torch
import torch.nn as nn
import torch.optim as optim
from torch.utils.data import DataLoader
from torchvision import datasets, transforms
from torchvision.models import resnet18, ResNet18_Weights
from sklearn.metrics import confusion_matrix
import matplotlib.pyplot as plt


def build_transforms(img_size: int = 224):
    imagenet_mean = [0.485, 0.456, 0.406]
    imagenet_std = [0.229, 0.224, 0.225]

    train_tf = transforms.Compose(
        [
            transforms.Resize((img_size, img_size)),
            transforms.RandomHorizontalFlip(p=0.5),
            transforms.RandomRotation(degrees=15),
            transforms.ColorJitter(brightness=0.15, contrast=0.15, saturation=0.15, hue=0.02),
            transforms.ToTensor(),
            transforms.Normalize(mean=imagenet_mean, std=imagenet_std),
        ]
    )

    eval_tf = transforms.Compose(
        [
            transforms.Resize((img_size, img_size)),
            transforms.ToTensor(),
            transforms.Normalize(mean=imagenet_mean, std=imagenet_std),
        ]
    )

    return train_tf, eval_tf


def accuracy_from_logits(logits: torch.Tensor, targets: torch.Tensor) -> float:
    preds = torch.argmax(logits, dim=1)
    correct = (preds == targets).sum().item()
    return correct / targets.size(0)


@torch.no_grad()
def evaluate(model: nn.Module, loader: DataLoader, device: torch.device, criterion: nn.Module):
    model.eval()

    total_loss = 0.0
    total_correct = 0
    total_samples = 0

    for images, targets in loader:
        images = images.to(device)
        targets = targets.to(device)

        logits = model(images)
        loss = criterion(logits, targets)

        bs = targets.size(0)
        total_loss += loss.item() * bs
        total_correct += (torch.argmax(logits, dim=1) == targets).sum().item()
        total_samples += bs

    avg_loss = total_loss / max(1, total_samples)
    avg_acc = total_correct / max(1, total_samples)
    return avg_loss, avg_acc


def set_trainable(model: nn.Module, fc_only: bool) -> None:
    """Geler/dégeler le backbone.

    - fc_only=True  -> seul model.fc est entraînable
    - fc_only=False -> tout le réseau est entraînable
    """
    if fc_only:
        for p in model.parameters():
            p.requires_grad = False
        for p in model.fc.parameters():
            p.requires_grad = True
    else:
        for p in model.parameters():
            p.requires_grad = True


@torch.no_grad()
def collect_predictions(model: nn.Module, loader: DataLoader, device: torch.device):
    """Collecter y_true et y_pred pour la matrice de confusion."""
    model.eval()
    y_true: list[int] = []
    y_pred: list[int] = []

    for images, targets in loader:
        images = images.to(device)
        targets = targets.to(device)
        logits = model(images)
        preds = torch.argmax(logits, dim=1)
        y_true.extend(targets.cpu().tolist())
        y_pred.extend(preds.cpu().tolist())

    return y_true, y_pred


def train_one_epoch(
    model: nn.Module,
    loader: DataLoader,
    device: torch.device,
    criterion: nn.Module,
    optimizer: optim.Optimizer,
):
    model.train()

    total_loss = 0.0
    total_correct = 0
    total_samples = 0

    for images, targets in loader:
        images = images.to(device)
        targets = targets.to(device)

        optimizer.zero_grad(set_to_none=True)
        logits = model(images)
        loss = criterion(logits, targets)
        loss.backward()
        optimizer.step()

        bs = targets.size(0)
        total_loss += loss.item() * bs
        total_correct += (torch.argmax(logits, dim=1) == targets).sum().item()
        total_samples += bs

    avg_loss = total_loss / max(1, total_samples)
    avg_acc = total_correct / max(1, total_samples)
    return avg_loss, avg_acc


def main():
    parser = argparse.ArgumentParser(description="Train ResNet18 on PlantVillage (PyTorch/ImageFolder).")
    parser.add_argument("--data-dir", default=None, help="Dataset root with train/val/test.")
    parser.add_argument("--epochs", type=int, default=12, help="Epochs (10-15 recommended).")
    parser.add_argument(
        "--freeze-epochs",
        type=int,
        default=5,
        help="Nombre d'epochs où seul le head (fc) est entraîné. 0 = pas de freeze. Défaut: 5",
    )
    parser.add_argument("--batch-size", type=int, default=32)
    parser.add_argument("--lr", type=float, default=1e-4)
    parser.add_argument("--weight-decay", type=float, default=1e-4)
    parser.add_argument("--img-size", type=int, default=224)
    parser.add_argument("--seed", type=int, default=42)
    parser.add_argument(
        "--output",
        default=None,
        help="Output model path (.pth). Default: ai_api/model_plantvillage.pth",
    )
    args = parser.parse_args()

    # Repro
    torch.manual_seed(args.seed)

    project_root = Path(__file__).resolve().parents[1]

    data_dir = Path(args.data_dir) if args.data_dir else (project_root / "dataset")
    if not data_dir.is_absolute():
        data_dir = project_root / data_dir

    train_dir = data_dir / "train"
    val_dir = data_dir / "val"
    test_dir = data_dir / "test"

    if not train_dir.exists() or not val_dir.exists() or not test_dir.exists():
        raise FileNotFoundError(
            "Dataset folders not found. Expected: "
            f"{train_dir}, {val_dir}, {test_dir}. "
            "Run prepare_data.py first."
        )

    output_path = Path(args.output) if args.output else (Path(__file__).resolve().parent / "model_plantvillage.pth")
    output_path.parent.mkdir(parents=True, exist_ok=True)
    classes_path = output_path.parent / "classes.json"

    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

    train_tf, eval_tf = build_transforms(img_size=args.img_size)

    print("=" * 70)
    print("AgroPRedi - PyTorch Training (ResNet18 / PlantVillage)")
    print("=" * 70)
    print(f"Data dir: {data_dir}")
    print(f"Device: {device}")
    print(f"Epochs: {args.epochs}")
    print(f"Freeze epochs: {args.freeze_epochs}")
    print(f"Batch size: {args.batch_size}")
    print(f"LR: {args.lr}")
    print(f"Weight decay: {args.weight_decay}")
    print(f"Output: {output_path}")
    print("=" * 70)

    # Datasets
    train_ds = datasets.ImageFolder(str(train_dir), transform=train_tf)
    val_ds = datasets.ImageFolder(str(val_dir), transform=eval_tf)
    test_ds = datasets.ImageFolder(str(test_dir), transform=eval_tf)

    num_classes = len(train_ds.classes)
    print(f"Detected classes: {num_classes}")
    print("Class names:")
    for i, c in enumerate(train_ds.classes):
        print(f"  {i}: {c}")

    # Save classes mapping for later inference (API will need this)
    with open(classes_path, "w", encoding="utf-8") as f:
        json.dump({"classes": train_ds.classes, "class_to_idx": train_ds.class_to_idx}, f, ensure_ascii=False, indent=2)
    print(f"Saved classes to: {classes_path}")

    # Loaders (Windows friendly: num_workers=0)
    train_loader = DataLoader(
        train_ds,
        batch_size=args.batch_size,
        shuffle=True,
        num_workers=0,
        pin_memory=torch.cuda.is_available(),
    )
    val_loader = DataLoader(
        val_ds,
        batch_size=args.batch_size,
        shuffle=False,
        num_workers=0,
        pin_memory=torch.cuda.is_available(),
    )
    test_loader = DataLoader(
        test_ds,
        batch_size=args.batch_size,
        shuffle=False,
        num_workers=0,
        pin_memory=torch.cuda.is_available(),
    )

    # Model: ResNet18 pretrained ImageNet
    # Note: first run may download weights if not cached.
    weights = ResNet18_Weights.IMAGENET1K_V1
    model = resnet18(weights=weights)
    model.fc = nn.Linear(model.fc.in_features, num_classes)
    model = model.to(device)

    criterion = nn.CrossEntropyLoss()

    # Phase 1: freeze backbone (optionnel)
    if args.freeze_epochs > 0:
        set_trainable(model, fc_only=True)
        optimizer = optim.Adam(filter(lambda p: p.requires_grad, model.parameters()), lr=args.lr, weight_decay=args.weight_decay)
        print("[INFO] Phase 1: backbone gelé, entraînement uniquement de la couche fc")
    else:
        set_trainable(model, fc_only=False)
        optimizer = optim.Adam(model.parameters(), lr=args.lr, weight_decay=args.weight_decay)
        print("[INFO] Freeze désactivé, entraînement du modèle complet dès le début")

    best_val_acc = -1.0
    best_epoch = -1
    start_time = time.time()

    for epoch in range(1, args.epochs + 1):
        # Phase 2: unfreeze complet (à partir de freeze_epochs+1)
        if args.freeze_epochs > 0 and epoch == args.freeze_epochs + 1:
            set_trainable(model, fc_only=False)
            optimizer = optim.Adam(model.parameters(), lr=args.lr, weight_decay=args.weight_decay)
            print("[INFO] Phase 2: backbone dégelé, entraînement du réseau complet")

        t0 = time.time()

        train_loss, train_acc = train_one_epoch(model, train_loader, device, criterion, optimizer)
        val_loss, val_acc = evaluate(model, val_loader, device, criterion)

        elapsed = time.time() - t0

        print(
            f"Epoch {epoch:02d}/{args.epochs} | "
            f"train loss={train_loss:.4f} acc={train_acc*100:.2f}% | "
            f"val loss={val_loss:.4f} acc={val_acc*100:.2f}% | "
            f"time={elapsed:.1f}s"
        )

        if val_acc > best_val_acc:
            best_val_acc = val_acc
            best_epoch = epoch
            checkpoint = {
                "arch": "resnet18",
                "num_classes": num_classes,
                "class_to_idx": train_ds.class_to_idx,
                "classes": train_ds.classes,
                "state_dict": model.state_dict(),
                "optimizer": optimizer.state_dict(),
                "epoch": epoch,
                "val_acc": float(val_acc),
            }
            torch.save(checkpoint, output_path)
            print(f"  -> Saved best checkpoint (val acc={best_val_acc*100:.2f}%) to {output_path}")

    total_elapsed = time.time() - start_time
    print("\nTraining finished.")
    print(f"Best val acc: {best_val_acc*100:.2f}% at epoch {best_epoch}")
    print(f"Total time: {total_elapsed/60:.1f} min")

    # Final test evaluation (load best model)
    if output_path.exists():
        best = torch.load(output_path, map_location=device)
        model.load_state_dict(best["state_dict"])

    test_loss, test_acc = evaluate(model, test_loader, device, criterion)
    print("\n================ TEST METRICS ================")
    print(f"Test loss: {test_loss:.4f}")
    print(f"Test accuracy: {test_acc*100:.2f}%")
    print("============================================\n")

    # Matrice de confusion (évaluation plus scientifique)
    y_true, y_pred = collect_predictions(model, test_loader, device)
    cm = confusion_matrix(y_true, y_pred, labels=list(range(num_classes)))

    print("================ CONFUSION MATRIX ================")
    print("Lignes = vraies classes, Colonnes = prédictions")
    print("Ordre des classes:")
    for i, c in enumerate(train_ds.classes):
        print(f"  {i}: {c}")
    print("\nMatrice (valeurs brutes):")
    print(cm)
    print("=================================================\n")

    # Sauvegarde en image
    cm_path = Path(__file__).resolve().parent / "confusion_matrix.png"
    plt.figure(figsize=(12, 10))
    plt.imshow(cm, interpolation="nearest", cmap=plt.cm.Blues)
    plt.title("Confusion Matrix (Test)")
    plt.colorbar()
    tick_marks = list(range(num_classes))
    plt.xticks(tick_marks, train_ds.classes, rotation=90)
    plt.yticks(tick_marks, train_ds.classes)
    plt.tight_layout()
    plt.ylabel("Vraie classe")
    plt.xlabel("Classe prédite")
    plt.savefig(cm_path, dpi=200)
    plt.close()
    print(f"Confusion matrix sauvegardée: {cm_path}")


if __name__ == "__main__":
    # Reduce OpenMP thread oversubscription on some Windows setups
    os.environ.setdefault("OMP_NUM_THREADS", "1")
    main()
