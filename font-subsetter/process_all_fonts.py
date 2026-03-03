#!/usr/bin/env python3
"""
Batch Font Subsetter - Process all fonts in fonts/ directory

Usage:
    python process_all_fonts.py --inputs sk-cz-test.txt
"""

import argparse
import subprocess
import sys
from pathlib import Path


def subset_font(font_path: Path, chars: str, out_dir: Path, family_name: str) -> bool:
    """Create font subsets in TTF, WOFF, WOFF2 formats."""
    out_dir.mkdir(parents=True, exist_ok=True)
    
    chars_file = out_dir / "_chars.txt"
    chars_file.write_text(chars, encoding="utf-8")
    
    python_exec = sys.executable
    
    try:
        # TTF
        cmd = [
            python_exec, "-m", "fontTools.subset",
            str(font_path),
            f"--text-file={chars_file}",
            "--layout-features=*",
            "--name-IDs=*",
            "--glyph-names",
            "--symbol-cmap",
            "--legacy-cmap",
            "--notdef-glyph",
            "--notdef-outline",
            f"--output-file={out_dir}/{family_name}.ttf",
        ]
        subprocess.run(cmd, check=True, capture_output=True)
        
        # WOFF
        cmd[-1] = f"--output-file={out_dir}/{family_name}.woff"
        cmd.append("--flavor=woff")
        subprocess.run(cmd, check=True, capture_output=True)
        
        # WOFF2
        cmd[-2] = f"--output-file={out_dir}/{family_name}.woff2"
        cmd[-3] = "--flavor=woff2"
        result = subprocess.run(cmd, capture_output=True, text=True)
        woff2_success = result.returncode == 0
        
        chars_file.unlink()
        return woff2_success
        
    except subprocess.CalledProcessError as e:
        print(f"  ❌ Error: {e}")
        if chars_file.exists():
            chars_file.unlink()
        return False


def generate_css(family_name: str, out_dir: Path) -> None:
    """Generate CSS @font-face declaration."""
    css = f"""@font-face {{
  font-family: '{family_name}';
  src:
    url('{family_name}.woff2') format('woff2'),
    url('{family_name}.woff') format('woff'),
    url('{family_name}.ttf') format('truetype');
  font-display: swap;
}}
"""
    (out_dir / f"{family_name}.css").write_text(css, encoding="utf-8")


def main():
    parser = argparse.ArgumentParser(description="Process all fonts in fonts/ directory")
    parser.add_argument(
        "--inputs", 
        nargs="+", 
        default=["sk-cz-test.txt"],
        help="Input files to analyze"
    )
    parser.add_argument(
        "--out", 
        default="output", 
        help="Output directory"
    )
    
    args = parser.parse_args()
    
    # Extract characters from input files
    print("📊 Analyzing input files...")
    chars = set()
    for pattern in args.inputs:
        for path in Path().glob(pattern) if '*' in pattern else [Path(pattern)]:
            if path.exists():
                data = path.read_text(encoding="utf-8", errors="ignore")
                if path.suffix.lower() == ".html":
                    from html.parser import HTMLParser
                    class TextExtractor(HTMLParser):
                        def __init__(self):
                            super().__init__()
                            self.text = []
                        def handle_data(self, data):
                            self.text.append(data)
                        def get_text(self):
                            return ''.join(self.text)
                    
                    parser = TextExtractor()
                    parser.feed(data)
                    data = parser.get_text()
                chars.update(set(data))
    
    # Filter control characters
    chars = {c for c in chars if ord(c) >= 32 or c in '\n\r\t'}
    chars_str = "".join(sorted(chars))
    
    print(f"📝 Extracted {len(chars_str)} unique characters")
    
    # Find all TTF files (prefer TTF over OTF, skip WEB duplicates)
    fonts_dir = Path("fonts")
    font_files = list(set(fonts_dir.rglob("*.ttf")) | set(fonts_dir.rglob("*.otf")))
    font_files = [
        p for p in sorted(font_files)
        if "/WEB/" not in str(p) and "/OTF/" not in str(p)
    ]
    
    print(f"🔍 Found {len(font_files)} font files")
    
    # Process each font
    print(f"\n🔧 Processing fonts...")
    out_dir = Path(args.out)
    
    results = []
    for font_path in font_files:
        # Create family name from path
        parts = font_path.relative_to(fonts_dir).parts
        # Get the font family directory name or use parent
        if len(parts) > 1:
            family_dir = parts[0]  # e.g., "Alpino_Complete"
        else:
            family_dir = font_path.parent.name
        
        # Clean up family name
        family_name = family_dir.replace('_Complete', '').replace('-Regular', '').replace('-Variable', '')
        # Also clean the font file name for variants
        font_stem = font_path.stem
        if font_stem != family_name:
            variant = font_stem.replace(family_name, '').replace('-', '')
            if variant:
                family_name = f"{family_name}-{variant}"
        
        # Determine category (sans-serif vs serif)
        category = "sans-serif" if "sans-serif" in str(font_path) else "serif"
        font_out_dir = out_dir / category / family_name.lower()
        
        print(f"\n  Processing: {font_path.relative_to(fonts_dir)}")
        success = subset_font(font_path, chars_str, font_out_dir, family_name)
        
        if success:
            generate_css(family_name, font_out_dir)
            
            # Get file sizes
            ttf_size = (font_out_dir / f"{family_name}.ttf").stat().st_size
            woff2_size = (font_out_dir / f"{family_name}.woff2").stat().st_size
            savings = (1 - woff2_size / ttf_size) * 100
            
            print(f"    ✅ {family_name} ({category})")
            print(f"       TTF: {ttf_size/1024:.1f}K → WOFF2: {woff2_size/1024:.1f}K ({savings:.0f}% smaller)")
            results.append((family_name, category, ttf_size, woff2_size, savings))
        else:
            print(f"    ❌ {family_name} - failed")
    
    # Summary
    print(f"\n{'='*70}")
    print(f"✅ Done! Processed {len(results)} fonts")
    print(f"📁 Output: {out_dir.absolute()}")
    
    if results:
        print(f"\n{'Font':<25} {'Category':<12} {'TTF':>8} {'WOFF2':>8} {'Savings':>10}")
        print(f"{'-'*70}")
        for name, cat, ttf, woff2, savings in results:
            print(f"{name:<25} {cat:<12} {ttf/1024:>7.1f}K {woff2/1024:>7.1f}K {savings:>9.1f}%")


if __name__ == "__main__":
    main()
