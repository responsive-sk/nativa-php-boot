#!/usr/bin/env python3
"""
Font Subsetter - Web font subset generator

Extracts characters from HTML/TXT files and creates optimized web fonts
in TTF, WOFF, and WOFF2 formats with CSS.
"""

import argparse
import pathlib
import subprocess
import sys
from html.parser import HTMLParser
from pathlib import Path


class TextExtractor(HTMLParser):
    """Extract text content from HTML files."""
    
    def __init__(self):
        super().__init__()
        self.text_content = []
        self._in_script_or_style = False
    
    def handle_starttag(self, tag, attrs):
        if tag.lower() in ('script', 'style'):
            self._in_script_or_style = True
    
    def handle_endtag(self, tag):
        if tag.lower() in ('script', 'style'):
            self._in_script_or_style = False
    
    def handle_data(self, data):
        if not self._in_script_or_style:
            self.text_content.append(data)
    
    def get_text(self) -> str:
        return ''.join(self.text_content)


def extract_chars_from_file(file_path: Path) -> set:
    """Extract unique characters from a file (HTML or TXT)."""
    data = file_path.read_text(encoding="utf-8", errors="ignore")
    
    if file_path.suffix.lower() == ".html":
        parser = TextExtractor()
        parser.feed(data)
        text = parser.get_text()
    else:
        text = data
    
    return set(text)


def extract_text_from_files(input_paths: list) -> str:
    """
    Extract unique characters from multiple files.
    
    Args:
        input_paths: List of file paths (HTML or TXT)
    
    Returns:
        Sorted string of unique characters
    """
    chars = set()
    
    for path_str in input_paths:
        path = Path(path_str)
        
        if path.is_file():
            chars.update(extract_chars_from_file(path))
        elif path.is_dir():
            for ext in ['*.html', '*.txt', '*.htm']:
                for file_path in path.glob(ext):
                    chars.update(extract_chars_from_file(file_path))
        else:
            print(f"⚠️  Warning: {path} does not exist, skipping")
    
    # Filter out control characters except common whitespace
    filtered_chars = {
        c for c in chars 
        if ord(c) >= 32 or c in '\n\r\t'
    }
    
    return "".join(sorted(filtered_chars))


def subset_font(font_path: str, chars: str, out_dir: Path, family_name: str) -> None:
    """
    Create font subsets in multiple formats.
    
    Args:
        font_path: Path to source font file (TTF/OTF)
        chars: String of characters to include in subset
        out_dir: Output directory
        family_name: Font family name for output files
    """
    out_dir.mkdir(parents=True, exist_ok=True)
    
    # Write chars to temp file to avoid shell escaping issues
    chars_file = out_dir / "_chars.txt"
    chars_file.write_text(chars, encoding="utf-8")
    
    # Use the same Python executable as this script
    python_exec = sys.executable
    
    # TTF - no flavor needed (default)
    out_file = out_dir / f"{family_name}.ttf"
    cmd = [
        python_exec, "-m", "fontTools.subset",
        font_path,
        f"--text-file={chars_file}",
        "--layout-features=*",
        "--name-IDs=*",
        "--glyph-names",
        "--symbol-cmap",
        "--legacy-cmap",
        "--notdef-glyph",
        "--notdef-outline",
        f"--output-file={out_file}",
    ]
    print("Generating TTF...")
    subprocess.run(cmd, check=True, capture_output=True)
    print(f"  ✅ Generated: {out_file}")
    
    # WOFF
    out_file = out_dir / f"{family_name}.woff"
    cmd = [
        python_exec, "-m", "fontTools.subset",
        font_path,
        f"--text-file={chars_file}",
        "--layout-features=*",
        "--name-IDs=*",
        "--glyph-names",
        "--symbol-cmap",
        "--legacy-cmap",
        "--notdef-glyph",
        "--notdef-outline",
        "--flavor=woff",
        f"--output-file={out_file}",
    ]
    print("Generating WOFF...")
    subprocess.run(cmd, check=True, capture_output=True)
    print(f"  ✅ Generated: {out_file}")
    
    # WOFF2
    out_file = out_dir / f"{family_name}.woff2"
    cmd = [
        python_exec, "-m", "fontTools.subset",
        font_path,
        f"--text-file={chars_file}",
        "--layout-features=*",
        "--name-IDs=*",
        "--glyph-names",
        "--symbol-cmap",
        "--legacy-cmap",
        "--notdef-glyph",
        "--notdef-outline",
        "--flavor=woff2",
        f"--output-file={out_file}",
    ]
    print("Generating WOFF2...")
    result = subprocess.run(cmd, capture_output=True, text=True)
    if result.returncode == 0:
        print(f"  ✅ Generated: {out_file}")
    else:
        print(f"  ⚠️  WOFF2 skipped (install brotli: pip install brotli)")
    
    # Cleanup temp file
    chars_file.unlink()


def generate_css(family_name: str, out_dir: Path) -> None:
    """
    Generate CSS @font-face declaration.
    
    Args:
        family_name: Font family name
        out_dir: Output directory
    """
    css = f"""@font-face {{
  font-family: '{family_name}';
  src:
    url('{family_name}.woff2') format('woff2'),
    url('{family_name}.woff') format('woff'),
    url('{family_name}.ttf') format('truetype');
  font-display: swap;
}}
"""
    css_file = out_dir / f"{family_name}.css"
    css_file.write_text(css, encoding="utf-8")
    print(f"  ✅ Generated CSS: {css_file}")


def main():
    parser = argparse.ArgumentParser(
        description="Web font subset generator",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python font_subsetter.py --font Roboto-Regular.ttf --inputs ./pages/*.html
  python font_subsetter.py --font MyFont.otf --inputs content.txt --family MyWebFont
  python font_subsetter.py --font font.ttf --inputs ./docs/ --out ./webfonts
        """
    )
    parser.add_argument(
        "--font", 
        required=True, 
        help="Input font file (TTF or OTF)"
    )
    parser.add_argument(
        "--inputs", 
        nargs="+", 
        required=True, 
        help="HTML/TXT files or directories to analyze"
    )
    parser.add_argument(
        "--out", 
        default="out_fonts", 
        help="Output directory (default: out_fonts)"
    )
    parser.add_argument(
        "--family", 
        default="SubsetFont", 
        help="Font family name for output files (default: SubsetFont)"
    )
    
    args = parser.parse_args()
    
    # Validate font file
    font_path = Path(args.font)
    if not font_path.exists():
        print(f"❌ Error: Font file '{font_path}' not found")
        return 1
    
    if font_path.suffix.lower() not in ['.ttf', '.otf']:
        print(f"❌ Error: Font must be TTF or OTF format")
        return 1
    
    print(f"📊 Analyzing input files...")
    chars = extract_text_from_files(args.inputs)
    print(f"📝 Extracted {len(chars)} unique characters")
    
    if len(chars) == 0:
        print("⚠️  Warning: No characters extracted from input files")
        return 1
    
    # Show character preview
    preview = chars[:50] + "..." if len(chars) > 50 else chars
    print(f"   Preview: '{preview}'")
    
    out_dir = Path(args.out)
    print(f"\n🔧 Generating font subsets...")
    subset_font(str(font_path), chars, out_dir, args.family)
    
    print(f"\n🎨 Generating CSS...")
    generate_css(args.family, out_dir)
    
    print(f"\n✅ Done! Output files in: {out_dir.absolute()}")
    print(f"   Files: {args.family}.{{ttf,woff,woff2,css}}")
    
    return 0


if __name__ == "__main__":
    exit(main())
