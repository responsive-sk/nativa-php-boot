#!/usr/bin/env python3
"""
Batch Font Subsetter - Process multiple fonts at once

Usage:
    python batch_subsetter.py --inputs sk-cz-test.txt --out output
"""

import argparse
import glob
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
    parser = argparse.ArgumentParser(
        description="Batch font subset generator",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
    python batch_subsetter.py --inputs sk-cz-test.txt --out output
    python batch_subsetter.py --inputs *.html --fonts fonts/*.ttf --out webfonts
        """
    )
    parser.add_argument(
        "--inputs", 
        nargs="+", 
        required=True, 
        help="HTML/TXT files to analyze"
    )
    parser.add_argument(
        "--fonts",
        nargs="+",
        default=["fonts/*.ttf"],
        help="Font files to process (default: fonts/*.ttf)"
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
    preview = chars_str[:60] + "..." if len(chars_str) > 60 else chars_str
    print(f"   Preview: '{preview}'")
    
    # Process each font
    print(f"\n🔧 Processing fonts...")
    out_dir = Path(args.out)
    
    font_files = []
    for pattern in args.fonts:
        if '*' in pattern:
            font_files.extend(Path("fonts").glob(pattern.split('/')[-1] if '/' in pattern else pattern))
        else:
            font_files.append(Path(pattern))
    
    results = []
    for font_path in sorted(set(font_files)):
        if not font_path.exists():
            print(f"⚠️  Font not found: {font_path}")
            continue
        
        family_name = font_path.stem.replace('-Regular', '').replace('-Bold', '').replace('-Italic', '')
        font_out_dir = out_dir / family_name.lower()
        
        print(f"\n  Processing: {font_path.name}")
        success = subset_font(font_path, chars_str, font_out_dir, family_name)
        
        if success:
            generate_css(family_name, font_out_dir)
            
            # Get file sizes
            ttf_size = (font_out_dir / f"{family_name}.ttf").stat().st_size
            woff2_size = (font_out_dir / f"{family_name}.woff2").stat().st_size
            savings = (1 - woff2_size / ttf_size) * 100
            
            print(f"    ✅ {family_name}")
            print(f"       TTF: {ttf_size/1024:.1f}K → WOFF2: {woff2_size/1024:.1f}K ({savings:.0f}% smaller)")
            results.append((family_name, ttf_size, woff2_size, savings))
        else:
            print(f"    ❌ {family_name} - failed")
    
    # Summary
    print(f"\n{'='*60}")
    print(f"✅ Done! Processed {len(results)} fonts")
    print(f"📁 Output: {out_dir.absolute()}")
    
    if results:
        print(f"\n{'Font':<20} {'TTF':>8} {'WOFF2':>8} {'Savings':>10}")
        print(f"{'-'*60}")
        for name, ttf, woff2, savings in results:
            print(f"{name:<20} {ttf/1024:>7.1f}K {woff2/1024:>7.1f}K {savings:>9.1f}%")


if __name__ == "__main__":
    main()
