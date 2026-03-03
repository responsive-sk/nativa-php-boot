#!/usr/bin/env python3
"""
Font Generator & Optimizer
Creates optimized font subsets for Nativa CMS
Uses custom sans-serif and serif font sets
"""

import os
import argparse
from pathlib import Path
from fontTools import subset
from datetime import datetime


class NativaFontOptimizer:
    """Optimizes fonts for Nativa CMS with custom character sets."""
    
    def __init__(self, input_font: str = "input.otf"):
        self.input_font = input_font
        self.output_dir = Path("fonts")
        self.output_dir.mkdir(exist_ok=True)
        
        # Character sets for Nativa CMS
        self.char_sets = {
            # =================================================================
            # SANS-SERIF (Primary for body text, UI, headlines)
            # =================================================================
            'sans-web': {
                'name': 'Sans-Serif Web',
                'desc': 'Optimalizovaná sans-serif sada pre web',
                'family': 'sans-serif',
                'chars': (
                    # Basic Latin
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    'abcdefghijklmnopqrstuvwxyz'
                    '0123456789'
                    # Slovak + Czech diacritics
                    'ÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽ'
                    'áäčďéíĺľňóôŕšťúýž'
                    'ĚŘŮěřů'
                    # Punctuation & symbols
                    ' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'
                    '€£¥¢©®™°±×÷'
                )
            },
            'sans-minimal': {
                'name': 'Sans-Serif Minimal',
                'desc': 'Minimálna sada pre UI komponenty',
                'family': 'sans-serif',
                'chars': (
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    'abcdefghijklmnopqrstuvwxyz'
                    '0123456789'
                    ' -'
                )
            },
            'sans-headings': {
                'name': 'Sans-Serif Headings',
                'desc': 'Optimalizovaná pre nadpisy',
                'family': 'sans-serif',
                'chars': (
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    '0123456789'
                    'ÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽ'
                    ' !".'
                )
            },
            
            # =================================================================
            # SERIF (For editorial, quotes, elegant text)
            # =================================================================
            'serif-web': {
                'name': 'Serif Web',
                'desc': 'Optimalizovaná serif sada pre web',
                'family': 'serif',
                'chars': (
                    # Basic Latin
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    'abcdefghijklmnopqrstuvwxyz'
                    '0123456789'
                    # Slovak + Czech diacritics
                    'ÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽ'
                    'áäčďéíĺľňóôŕšťúýž'
                    'ĚŘŮěřů'
                    # Punctuation & symbols
                    ' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'
                    '€£¥¢©®™°±×÷'
                )
            },
            'serif-minimal': {
                'name': 'Serif Minimal',
                'desc': 'Minimálna sada pre serifové akcenty',
                'family': 'serif',
                'chars': (
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    'abcdefghijklmnopqrstuvwxyz'
                    'ÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽ'
                    'áäčďéíĺľňóôŕšťúýž'
                )
            },
            'serif-editorial': {
                'name': 'Serif Editorial',
                'desc': 'Pre články, citácie, elegantný text',
                'family': 'serif',
                'chars': (
                    'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                    'abcdefghijklmnopqrstuvwxyz'
                    '0123456789'
                    'ÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽ'
                    'áäčďéíĺľňóôŕšťúýž'
                    'ĚŘŮěřů'
                    ' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'
                    '€£¥¢©®™°±×÷'
                )
            },
        }

    def chars_to_unicode_range(self, text: str) -> str:
        """Convert characters to unicode range format."""
        unique_chars = sorted(set(text))
        ranges = []
        
        if not unique_chars:
            return ""
        
        codes = [ord(c) for c in unique_chars if c.isprintable()]
        codes.sort()
        
        if not codes:
            return ""
        
        start = codes[0]
        end = start
        
        for code in codes[1:]:
            if code == end + 1:
                end = code
            else:
                if start == end:
                    ranges.append(f"U+{start:04X}")
                else:
                    ranges.append(f"U+{start:04X}-{end:04X}")
                start = end = code
        
        if start == end:
            ranges.append(f"U+{start:04X}")
        else:
            ranges.append(f"U+{start:04X}-{end:04X}")
        
        return ','.join(ranges)

    def create_subset(self, charset_key: str, output_name: str = None) -> dict:
        """Create font subset for given character set."""
        
        if charset_key not in self.char_sets:
            print(f"❌ Character set '{charset_key}' not found")
            return {}
        
        char_set = self.char_sets[charset_key]
        chars = char_set['chars']
        
        if not output_name:
            output_name = f"font-{charset_key}"
        
        unicode_range = self.chars_to_unicode_range(chars)
        
        if not unicode_range:
            print(f"❌ No valid characters in {char_set['name']}")
            return {}
        
        # Create family subdirectory
        family_dir = self.output_dir / char_set['family']
        family_dir.mkdir(exist_ok=True)
        
        output_file = family_dir / f"{output_name}.woff2"
        
        # Subset arguments
        args = [
            str(self.input_font),
            f"--output-file={output_file}",
            f"--unicodes={unicode_range}",
            "--flavor=woff2",
            "--layout-features=*",
            "--desubroutinize",
            "--no-hinting",
            "--obfuscate-names",
            "--recommended-glyphs",
        ]
        
        try:
            subset.main(args)
            
            # Stats
            file_size = output_file.stat().st_size / 1024
            char_count = len(set(chars))
            
            result = {
                'file': str(output_file),
                'size_kb': round(file_size, 1),
                'char_count': char_count,
                'name': char_set['name'],
                'desc': char_set['desc'],
                'family': char_set['family']
            }
            
            print(f"✅ {char_set['name']}:")
            print(f"   📁 {output_file.name}")
            print(f"   🔤 {char_count} znakov")
            print(f"   📊 {file_size:.1f} KB")
            
            return result
            
        except Exception as e:
            print(f"❌ Error creating {char_set['name']}: {e}")
            return {}

    def create_css(self, font_results: list, output_file: str = "fonts/styles.css"):
        """Generate CSS file with @font-face rules."""
        
        css = f"""/* ============================================================================
   Nativa CMS - Generated Font CSS
   Created: {datetime.now().strftime('%Y-%m-%d %H:%M')}
   Optimized for SK/CZ languages with sans-serif and serif sets
   ============================================================================ */

"""
        
        # Group by family
        sans_fonts = [r for r in font_results if r and r.get('family') == 'sans-serif']
        serif_fonts = [r for r in font_results if r and r.get('family') == 'serif']
        
        # Sans-serif fonts
        css += """/* ============================================================================
   SANS-SERIF FONTS
   ============================================================================ */

"""
        
        for result in sans_fonts:
            font_name = result['file'].split('/')[-1].replace('.woff2', '')
            css += f"""/* {result['name']}
   {result['desc']}
   Characters: {result['char_count']} | Size: {result['size_kb']}KB
*/
@font-face {{
  font-family: '{font_name}';
  src: url('{font_name}.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}}

"""
        
        # Serif fonts
        css += """/* ============================================================================
   SERIF FONTS
   ============================================================================ */

"""
        
        for result in serif_fonts:
            font_name = result['file'].split('/')[-1].replace('.woff2', '')
            css += f"""/* {result['name']}
   {result['desc']}
   Characters: {result['char_count']} | Size: {result['size_kb']}KB
*/
@font-face {{
  font-family: '{font_name}';
  src: url('{font_name}.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}}

"""
        
        # Utility classes
        css += """/* ============================================================================
   UTILITY CLASSES
   ============================================================================ */

/* Primary sans-serif for body text */
.font-sans { font-family: 'font-sans-web', sans-serif; }

/* Serif for editorial content */
.font-serif { font-family: 'font-serif-web', serif; }

/* Minimal for UI */
.font-ui { font-family: 'font-sans-minimal', sans-serif; }

/* Headings */
.font-heading { font-family: 'font-sans-headings', sans-serif; }

/* Editorial serif */
.font-editorial { font-family: 'font-serif-editorial', serif; }
"""
        
        output_path = Path(output_file)
        output_path.parent.mkdir(parents=True, exist_ok=True)
        
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(css)
        
        print(f"\n📝 CSS generated: {output_file}")
        return str(output_path)

    def create_test_html(self, font_results: list):
        """Generate test HTML page."""
        
        sans_fonts = [r for r in font_results if r and r.get('family') == 'sans-serif']
        serif_fonts = [r for r in font_results if r and r.get('family') == 'serif']
        
        html = f"""<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Nativa Font Test - {datetime.now().strftime('%Y-%m-%d')}</title>
    <link rel="stylesheet" href="fonts/styles.css">
    <style>
        :root {{
            --sans-color: #2563eb;
            --serif-color: #dc2626;
            --bg-color: #f9fafb;
            --card-bg: #ffffff;
        }}

        * {{ box-sizing: border-box; }}

        body {{
            font-family: system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 2rem;
            background: var(--bg-color);
            line-height: 1.6;
            color: #1f2937;
        }}

        .container {{
            max-width: 1200px;
            margin: 0 auto;
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }}

        h1 {{ margin-top: 0; }}
        h2 {{ border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; margin-top: 2rem; }}

        .font-section {{
            margin: 2rem 0;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 5px solid;
        }}

        .sans {{ border-color: var(--sans-color); background: #eff6ff; }}
        .serif {{ border-color: var(--serif-color); background: #fef2f2; }}

        .variant {{
            margin: 1rem 0;
            padding: 1rem;
            border: 1px dashed #d1d5db;
            border-radius: 6px;
            background: rgba(255,255,255,0.7);
        }}

        .sample-text {{
            font-size: 1.25rem;
            padding: 0.75rem;
            background: white;
            border-radius: 4px;
            margin: 0.5rem 0;
        }}

        .stats {{
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
            flex-wrap: wrap;
        }}

        .stat {{
            padding: 0.25rem 0.75rem;
            background: rgba(0,0,0,0.05);
            border-radius: 3px;
        }}

        pre {{
            background: #1f2937;
            color: #f3f4f6;
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
        }}
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Nativa Font Test</h1>
        <p>Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}</p>
        <p>Input: <code>{self.input_font}</code> | Fonts: <strong>{len([r for r in font_results if r])}</strong></p>

        <div class="font-section sans">
            <h2>📝 Sans-Serif Fonts</h2>
            <p>Primary for body text, UI, headlines</p>
"""
        
        for font in sans_fonts:
            font_name = font['file'].split('/')[-1].replace('.woff2', '')
            html += f"""
            <div class="variant">
                <h3>{font['name']}</h3>
                <p>{font['desc']}</p>
                <div class="sample-text" style="font-family: '{font_name}';">
                    Vitajte na našej stránke! The quick brown fox jumps over the lazy dog.
                </div>
                <div class="stats">
                    <span class="stat">🔤 {font['char_count']} znakov</span>
                    <span class="stat">📊 {font['size_kb']}KB</span>
                </div>
            </div>
"""
        
        html += """
        </div>

        <div class="font-section serif">
            <h2>📖 Serif Fonts</h2>
            <p>For editorial content, quotes, elegant text</p>
"""
        
        for font in serif_fonts:
            font_name = font['file'].split('/')[-1].replace('.woff2', '')
            html += f"""
            <div class="variant">
                <h3>{font['name']}</h3>
                <p>{font['desc']}</p>
                <div class="sample-text" style="font-family: '{font_name}';">
                    "Elegancia a klasický štýl pre náročné projekty."
                </div>
                <div class="stats">
                    <span class="stat">🔤 {font['char_count']} znakov</span>
                    <span class="stat">📊 {font['size_kb']}KB</span>
                </div>
            </div>
"""
        
        html += f"""
        </div>

        <div class="variant">
            <h2>📋 CSS Usage</h2>
            <pre><code>/* Import fonts */
@import url('fonts/styles.css');

/* Use sans-serif for body */
body {{
    font-family: 'font-sans-web', sans-serif;
}}

/* Use serif for editorial */
.article {{
    font-family: 'font-serif-editorial', serif;
}}

/* Use for headings */
h1, h2 {{
    font-family: 'font-sans-headings', sans-serif;
}}</code></pre>
        </div>
    </div>
</body>
</html>"""
        
        output_file = Path("font-test.html")
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(html)
        
        print(f"\n🌐 Test HTML: {output_file}")
        return str(output_file)


def main():
    parser = argparse.ArgumentParser(
        description='Nativa Font Generator',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python3 create_font_from_html.py              # Generate all font sets
  python3 create_font_from_html.py --sans-only  # Generate only sans-serif
  python3 create_font_from_html.py --serif-only # Generate only serif
  python3 create_font_from_html.py --web-only   # Generate only web sets
        """
    )
    
    parser.add_argument('--input', '-i', default='input.otf', help='Input font file')
    parser.add_argument('--sans-only', action='store_true', help='Generate only sans-serif')
    parser.add_argument('--serif-only', action='store_true', help='Generate only serif')
    parser.add_argument('--web-only', action='store_true', help='Generate only web sets')
    
    args = parser.parse_args()
    
    if not Path(args.input).exists():
        print(f"❌ Input font not found: {args.input}")
        print("   Place your font as 'input.otf' in this directory")
        return 1
    
    print("=" * 70)
    print("🎨 NATIVA FONT GENERATOR")
    print("=" * 70)
    print(f"\n📁 Input: {args.input}")
    print(f"📂 Output: fonts/")
    print()
    
    optimizer = NativaFontOptimizer(args.input)
    results = []
    
    # Determine sets to generate
    if args.sans_only:
        sets = ['sans-web', 'sans-minimal', 'sans-headings']
    elif args.serif_only:
        sets = ['serif-web', 'serif-minimal', 'serif-editorial']
    elif args.web_only:
        sets = ['sans-web', 'serif-web']
    else:
        sets = ['sans-web', 'sans-minimal', 'sans-headings', 'serif-web', 'serif-minimal', 'serif-editorial']
    
    print("🎯 Generating fonts:")
    print("-" * 70)
    
    for charset_key in sets:
        result = optimizer.create_subset(charset_key)
        results.append(result)
        print()
    
    print("-" * 70)
    optimizer.create_css(results)
    optimizer.create_test_html(results)
    
    # Summary
    print("\n" + "=" * 70)
    print("✅ GENERATION COMPLETE")
    print("=" * 70)
    
    total_size = sum(r['size_kb'] for r in results if r)
    total_fonts = len([r for r in results if r])
    
    print(f"\n📊 Summary:")
    print(f"   📁 Fonts: {total_fonts}")
    print(f"   💾 Size: {total_size:.1f} KB")
    print(f"   📂 Output: fonts/")
    
    return 0


if __name__ == "__main__":
    exit(main())
