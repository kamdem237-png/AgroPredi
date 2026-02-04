import argparse
import os
import random
import shutil
from pathlib import Path


DEFAULT_ALLOWED_EXTS = {".jpg", ".jpeg", ".png", ".bmp", ".gif", ".tif", ".tiff", ".webp"}


def _find_plantvillage_root(project_root: Path) -> Path:
    """Try common locations for PlantVillage."""
    candidates = [
        project_root / "data" / "PlantVillage",
        project_root / "data" / "PlantVillage" / "PlantVillage",
    ]
    for c in candidates:
        if c.exists() and c.is_dir():
            return c
    raise FileNotFoundError(
        "PlantVillage folder not found. Expected one of: " + ", ".join(str(c) for c in candidates)
    )


def _iter_class_dirs(source_dir: Path):
    for p in sorted(source_dir.iterdir()):
        if p.is_dir() and not p.name.startswith("."):
            yield p


def _class_is_selected(class_name: str, allowed_plants: list[str]) -> bool:
    # PlantVillage naming is typically like:
    # Tomato_Late_blight, Corn___Common_rust, Pepper__bell___healthy, etc.
    # We accept classes whose folder name starts with the plant token (case-insensitive).
    lower = class_name.lower()
    for plant in allowed_plants:
        if lower.startswith(plant.lower()):
            return True
    return False


def _list_images(class_dir: Path, allowed_exts: set[str]) -> list[Path]:
    out: list[Path] = []
    for p in class_dir.iterdir():
        if p.is_file() and p.suffix.lower() in allowed_exts:
            out.append(p)
    return out


def _safe_mkdir(p: Path):
    p.mkdir(parents=True, exist_ok=True)


def _materialize(src: Path, dst: Path, mode: str):
    """Create dst from src without unnecessary duplication.

    mode:
      - hardlink: os.link (fallback to copy2)
      - symlink: os.symlink (fallback to copy2)
      - copy: shutil.copy2
    """
    if mode == "copy":
        shutil.copy2(src, dst)
        return

    try:
        if mode == "hardlink":
            os.link(src, dst)
            return
        if mode == "symlink":
            os.symlink(src, dst)
            return
    except Exception:
        # Fallback to copy (permissions/filesystem can block linking)
        shutil.copy2(src, dst)
        return

    raise ValueError(f"Unknown mode: {mode}")


def prepare_dataset(
    source_dir: Path,
    target_dir: Path,
    plants: list[str],
    train_ratio: float,
    val_ratio: float,
    test_ratio: float,
    seed: int,
    mode: str,
    allowed_exts: set[str],
    dry_run: bool,
):
    if abs((train_ratio + val_ratio + test_ratio) - 1.0) > 1e-6:
        raise ValueError("train/val/test ratios must sum to 1.0")

    rng = random.Random(seed)

    selected_classes: dict[str, list[Path]] = {}
    for class_dir in _iter_class_dirs(source_dir):
        if _class_is_selected(class_dir.name, plants):
            imgs = _list_images(class_dir, allowed_exts)
            if imgs:
                selected_classes[class_dir.name] = imgs

    if not selected_classes:
        raise RuntimeError(
            "No classes matched your plant filter. "
            f"Plants requested: {plants}. "
            f"Source dir: {source_dir}"
        )

    # Build folder structure compatible with torchvision.datasets.ImageFolder
    for split in ("train", "val", "test"):
        for class_name in selected_classes.keys():
            _safe_mkdir(target_dir / split / class_name)

    summary = {"train": {}, "val": {}, "test": {}}

    for class_name, images in selected_classes.items():
        images = list(images)
        rng.shuffle(images)

        n = len(images)
        n_train = int(n * train_ratio)
        n_val = int(n * val_ratio)
        # remainder goes to test
        n_test = n - n_train - n_val

        splits = {
            "train": images[:n_train],
            "val": images[n_train : n_train + n_val],
            "test": images[n_train + n_val :],
        }

        for split, files in splits.items():
            summary[split][class_name] = len(files)
            for src in files:
                dst = target_dir / split / class_name / src.name
                if dry_run:
                    continue
                _materialize(src, dst, mode)

        # Basic sanity per class
        if n_train == 0 or n_val == 0 or n_test == 0:
            # Don’t fail hard; print warning instead (small classes exist in PlantVillage)
            print(
                f"WARNING: class '{class_name}' has small split sizes "
                f"(train={n_train}, val={n_val}, test={n_test}, total={n})."
            )

    return summary


def _print_summary(summary: dict):
    def _tot(split: str) -> int:
        return sum(summary[split].values())

    print("\n================ DATASET SUMMARY ================")
    for split in ("train", "val", "test"):
        print(f"{split}: {len(summary[split])} classes, { _tot(split) } images")

    print("\nPer-class counts (train/val/test):")
    classes = sorted(summary["train"].keys())
    for c in classes:
        print(
            f"- {c}: "
            f"{summary['train'].get(c, 0)}/"
            f"{summary['val'].get(c, 0)}/"
            f"{summary['test'].get(c, 0)}"
        )
    print("=================================================\n")


def main():
    parser = argparse.ArgumentParser(description="Prepare PlantVillage dataset for PyTorch ImageFolder.")
    parser.add_argument(
        "--source",
        default=None,
        help="Path to PlantVillage root folder. Default: auto-detect under project data/.",
    )
    parser.add_argument(
        "--target",
        default="dataset",
        help="Output folder (train/val/test will be created inside). Default: dataset",
    )
    parser.add_argument(
        "--plants",
        nargs="+",
        default=["Corn", "Tomato"],
        help="Plants to keep (matches class folder prefix). Default: Corn Tomato",
    )
    parser.add_argument("--train", type=float, default=0.8)
    parser.add_argument("--val", type=float, default=0.1)
    parser.add_argument("--test", type=float, default=0.1)
    parser.add_argument("--seed", type=int, default=42)
    parser.add_argument(
        "--mode",
        choices=["hardlink", "copy", "symlink"],
        default="hardlink",
        help="How to materialize files in dataset/ to avoid duplication. Default: hardlink",
    )
    parser.add_argument(
        "--clean",
        action="store_true",
        help="Delete target dataset/ before creating it.",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Compute splits and print summary without writing files.",
    )

    args = parser.parse_args()

    project_root = Path(__file__).resolve().parents[1]
    source_dir = Path(args.source) if args.source else _find_plantvillage_root(project_root)
    target_dir = Path(args.target)
    if not target_dir.is_absolute():
        target_dir = project_root / target_dir

    if args.clean and target_dir.exists() and target_dir.is_dir() and not args.dry_run:
        shutil.rmtree(target_dir)

    print("================ AgroPRedi: prepare_data.py ================")
    print(f"Source: {source_dir}")
    print(f"Target: {target_dir}")
    print(f"Plants: {args.plants}")
    print(f"Split: train={args.train}, val={args.val}, test={args.test}")
    print(f"Mode: {args.mode}")
    print("============================================================")

    summary = prepare_dataset(
        source_dir=source_dir,
        target_dir=target_dir,
        plants=args.plants,
        train_ratio=args.train,
        val_ratio=args.val,
        test_ratio=args.test,
        seed=args.seed,
        mode=args.mode,
        allowed_exts=DEFAULT_ALLOWED_EXTS,
        dry_run=args.dry_run,
    )

    _print_summary(summary)


if __name__ == "__main__":
    main()
