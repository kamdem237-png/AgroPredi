import argparse
import random
import shutil
from pathlib import Path


ALLOWED_EXTS = {".jpg", ".jpeg", ".png"}


def list_images(class_dir: Path) -> list[Path]:
    images: list[Path] = []
    for p in class_dir.iterdir():
        if p.is_file() and p.suffix.lower() in ALLOWED_EXTS:
            images.append(p)
    return images


def safe_mkdir(p: Path) -> None:
    p.mkdir(parents=True, exist_ok=True)


def clean_dir(p: Path) -> None:
    if not p.exists():
        return
    for child in p.iterdir():
        if child.is_dir():
            shutil.rmtree(child)
        else:
            child.unlink(missing_ok=True)


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Split PlantVillage classes into dataset/train and dataset/val (ImageFolder compatible)."
    )
    parser.add_argument(
        "--source",
        default=None,
        help="Source root containing class folders. Default: auto-detect data/PlantVillage.",
    )
    parser.add_argument(
        "--target",
        default="dataset",
        help="Target root where train/ and val/ will be created. Default: dataset",
    )
    parser.add_argument("--train-ratio", type=float, default=0.8)
    parser.add_argument("--seed", type=int, default=42)
    parser.add_argument(
        "--clean",
        action="store_true",
        help="Clean dataset/train and dataset/val before copying.",
    )

    args = parser.parse_args()

    project_root = Path(__file__).resolve().parents[1]

    source_dir = Path(args.source) if args.source else (project_root / "data" / "PlantVillage")
    if not source_dir.is_absolute():
        source_dir = project_root / source_dir

    # Handle nested PlantVillage/PlantVillage if present
    nested = source_dir / "PlantVillage"
    if nested.exists() and nested.is_dir():
        # Prefer the folder that actually contains class dirs.
        # If source_dir has many class folders already, keep it.
        # Otherwise, use the nested one.
        has_classes = any(p.is_dir() for p in source_dir.iterdir())
        if not has_classes:
            source_dir = nested

    target_root = Path(args.target)
    if not target_root.is_absolute():
        target_root = project_root / target_root

    train_root = target_root / "train"
    val_root = target_root / "val"

    safe_mkdir(train_root)
    safe_mkdir(val_root)

    if args.clean:
        clean_dir(train_root)
        clean_dir(val_root)

    rng = random.Random(args.seed)

    class_dirs = [p for p in sorted(source_dir.iterdir()) if p.is_dir() and not p.name.startswith(".")]

    # Filter out obvious non-class container folders
    class_dirs = [p for p in class_dirs if p.name.lower() not in {"plantvillage"}]

    print("=" * 70)
    print("AgroPRedi - split_dataset.py")
    print("=" * 70)
    print(f"Source: {source_dir}")
    print(f"Target train: {train_root}")
    print(f"Target val:   {val_root}")
    print(f"Train ratio: {args.train_ratio}")
    print(f"Seed: {args.seed}")
    print(f"Clean: {args.clean}")
    print("=" * 70)

    if not class_dirs:
        raise FileNotFoundError(f"Aucune classe trouvée dans: {source_dir}")

    summary: dict[str, tuple[int, int, int]] = {}

    for class_dir in class_dirs:
        class_name = class_dir.name
        images = list_images(class_dir)

        if not images:
            print(f"WARNING: classe sans images: {class_name}")
            summary[class_name] = (0, 0, 0)
            continue

        rng.shuffle(images)
        n = len(images)
        n_train = int(n * args.train_ratio)
        n_val = n - n_train

        train_imgs = images[:n_train]
        val_imgs = images[n_train:]

        out_train = train_root / class_name
        out_val = val_root / class_name
        safe_mkdir(out_train)
        safe_mkdir(out_val)

        for src in train_imgs:
            shutil.copy2(src, out_train / src.name)

        for src in val_imgs:
            shutil.copy2(src, out_val / src.name)

        summary[class_name] = (n, n_train, n_val)

    print("\n================ RESUME ================")
    print(f"Classes détectées: {len(summary)}")
    for class_name in sorted(summary.keys()):
        total, n_train, n_val = summary[class_name]
        print(f"- {class_name}: total={total}, train={n_train}, val={n_val}")
    print("======================================\n")


if __name__ == "__main__":
    main()
