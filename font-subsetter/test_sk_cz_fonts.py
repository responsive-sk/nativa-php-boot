#!/usr/bin/env python3
"""
Test fonty pre SK/CZ znakovú sadu
"""

import sys
from pathlib import Path
from fontTools.ttLib import TTFont

# SK/CZ znaky na testovanie
SK_CZ_CHARS = (
    "ÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽ"  # Uppercase
    "áäčďéíĺľňóôŕšťúýž"  # Lowercase
    "ĚŘŮěřů"              # Czech specific
)

def test_font(font_path: Path) -> tuple[int, int, list[str]]:
    """Test if font contains all SK/CZ characters."""
    try:
        font = TTFont(font_path)
        cmap = font.getBestCmap()
        
        missing = []
        for char in SK_CZ_CHARS:
            if ord(char) not in cmap:
                missing.append(char)
        
        total = len(SK_CZ_CHARS)
        found = total - len(missing)
        
        return found, total, missing
    except Exception as e:
        return 0, len(SK_CZ_CHARS), [f"Error: {e}"]


def main():
    fonts_dir = Path("fonts")
    
    print("=" * 70)
    print("🔤 SK/CZ FONT TEST")
    print("=" * 70)
    
    results = []
    
    # Find all font files
    font_files = set(fonts_dir.rglob("*.ttf")) | set(fonts_dir.rglob("*.otf"))
    for font_path in sorted(font_files):
        # Skip duplicate WEB fonts if TTF exists
        if "/WEB/" in str(font_path) or "/OTF/" in str(font_path):
            continue
            
        found, total, missing = test_font(font_path)
        pct = (found / total * 100) if total > 0 else 0
        
        status = "✅" if found == total else "⚠️" if found > total * 0.8 else "❌"
        
        rel_path = font_path.relative_to(fonts_dir)
        print(f"\n{status} {rel_path}")
        print(f"   SK/CZ znaky: {found}/{total} ({pct:.1f}%)")
        
        if missing and len(missing) <= 10:
            print(f"   Chýba: {' '.join(missing)}")
        elif missing:
            print(f"   Chýba: {len(missing)} znakov ({missing[:5]}...)")
        
        results.append((font_path, found, total, missing))
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 ZHRNUTIE")
    print("=" * 70)
    
    complete = sum(1 for _, f, t, _ in results if f == t)
    partial = sum(1 for _, f, t, _ in results if f > t * 0.8 and f < t)
    poor = sum(1 for _, f, t, _ in results if f <= t * 0.8)
    
    print(f"\n✅ Kompletné: {complete}")
    print(f"⚠️  Čiastočné: {partial}")
    print(f"❌ Nedostatočné: {poor}")
    
    if complete > 0:
        print(f"\n🎉 {complete} fontov podporuje všetky SK/CZ znaky!")
    
    return 0 if complete > 0 else 1


if __name__ == "__main__":
    sys.exit(main())
