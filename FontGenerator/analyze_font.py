#!/usr/bin/env python3
"""
Font Analyzer - Detailed analysis of font files
Shows unicode ranges, character coverage, and file statistics
"""

import argparse
from pathlib import Path
from fontTools.ttLib import TTFont
import unicodedata


class FontAnalyzer:
    """Analyzes font files for character coverage and statistics."""
    
    # Unicode blocks for categorization
    UNICODE_BLOCKS = {
        'Basic Latin': (0x0000, 0x007F),
        'Latin-1 Supplement': (0x0080, 0x00FF),
        'Latin Extended-A': (0x0100, 0x017F),
        'Latin Extended-B': (0x0180, 0x024F),
        'IPA Extensions': (0x0250, 0x02AF),
        'Spacing Modifier': (0x02B0, 0x02FF),
        'Combining Diacritical': (0x0300, 0x036F),
        'Greek and Coptic': (0x0370, 0x03FF),
        'Cyrillic': (0x0400, 0x04FF),
        'Armenian': (0x0530, 0x058F),
        'Hebrew': (0x0590, 0x05FF),
        'Arabic': (0x0600, 0x06FF),
        'Devanagari': (0x0900, 0x097F),
        'Number Forms': (0x2150, 0x218F),
        'Arrows': (0x2190, 0x21FF),
        'Mathematical Operators': (0x2200, 0x22FF),
        'Misc Technical': (0x2300, 0x23FF),
        'Control Pictures': (0x2400, 0x243F),
        'Currency Symbols': (0x20A0, 0x20CF),
        'Letterlike Symbols': (0x2100, 0x214F),
        'General Punctuation': (0x2000, 0x206F),
        'Superscripts/Subscripts': (0x2070, 0x209F),
        'Geometric Shapes': (0x25A0, 0x25FF),
    }
    
    # Language-specific character ranges
    LANGUAGE_RANGES = {
        'Slovak (SK)': [
            (0x0041, 0x005A),  # A-Z
            (0x0061, 0x007A),  # a-z
            (0x00C1, 0x00C1),  # Á
            (0x00C4, 0x00C4),  # Ä
            (0x010C, 0x010D),  # Čč
            (0x010E, 0x010F),  # Ďď
            (0x00C9, 0x00C9),  # É
            (0x00CD, 0x00CD),  # Í
            (0x0139, 0x013A),  # Ĺĺ
            (0x013D, 0x013E),  # Ľľ
            (0x0147, 0x0148),  # Ňň
            (0x00D3, 0x00D3),  # Ó
            (0x00D4, 0x00D4),  # Ô
            (0x0154, 0x0155),  # Ŕŕ
            (0x0160, 0x0161),  # Šš
            (0x0164, 0x0165),  # Ťť
            (0x00DA, 0x00DA),  # Ú
            (0x00DD, 0x00DD),  # Ý
            (0x017D, 0x017E),  # Žž
        ],
        'Czech (CZ)': [
            (0x0041, 0x005A),  # A-Z
            (0x0061, 0x007A),  # a-z
            (0x00C1, 0x00C1),  # Á
            (0x010C, 0x010D),  # Čč
            (0x010E, 0x010F),  # Ďď
            (0x00C9, 0x00C9),  # É
            (0x011A, 0x011B),  # Ěě
            (0x00CD, 0x00CD),  # Í
            (0x0147, 0x0148),  # Ňň
            (0x00D3, 0x00D3),  # Ó
            (0x0158, 0x0159),  # Řř
            (0x0160, 0x0161),  # Šš
            (0x0164, 0x0165),  # Ťť
            (0x00DA, 0x00DA),  # Ú
            (0x016E, 0x016F),  # Ůů
            (0x00DD, 0x00DD),  # Ý
            (0x017D, 0x017E),  # Žž
        ],
        'English (EN)': [
            (0x0041, 0x005A),  # A-Z
            (0x0061, 0x007A),  # a-z
        ]
    }
    
    def __init__(self, font_path: str):
        self.font_path = Path(font_path)
        if not self.font_path.exists():
            raise FileNotFoundError(f"Font not found: {font_path}")
        
        self.font = TTFont(font_path)
        self.cmap = self.font.get('cmap')
        
    def get_unicode_map(self) -> dict:
        """Extract unicode character map from font."""
        unicode_map = {}
        
        if not self.cmap:
            return unicode_map
        
        for table in self.cmap.tables:
            if table.isUnicode():
                unicode_map.update(table.cmap)
        
        return unicode_map
    
    def analyze_blocks(self, unicode_map: dict) -> dict:
        """Categorize characters by unicode blocks."""
        blocks = {}
        
        for code in unicode_map.keys():
            block_name = self._get_block_name(code)
            
            if block_name not in blocks:
                blocks[block_name] = []
            
            try:
                char = chr(code)
                name = unicodedata.name(char, 'UNKNOWN')
                blocks[block_name].append({
                    'code': code,
                    'char': char,
                    'name': name
                })
            except:
                continue
        
        return blocks
    
    def _get_block_name(self, code: int) -> str:
        """Determine unicode block for a code point."""
        for block_name, (start, end) in self.UNICODE_BLOCKS.items():
            if start <= code <= end:
                return block_name
        return 'Other'
    
    def check_language_support(self, unicode_map: dict) -> dict:
        """Check which languages the font supports."""
        support = {}
        
        for lang, ranges in self.LANGUAGE_RANGES.items():
            required_chars = set()
            found_chars = set()
            
            # Collect all required characters for this language
            for start, end in ranges:
                for code in range(start, end + 1):
                    required_chars.add(code)
            
            # Check which ones are in the font
            for code in required_chars:
                if code in unicode_map:
                    found_chars.add(code)
            
            coverage = len(found_chars) / len(required_chars) * 100 if required_chars else 0
            
            support[lang] = {
                'required': len(required_chars),
                'found': len(found_chars),
                'missing': len(required_chars) - len(found_chars),
                'coverage': round(coverage, 1),
                'supported': coverage >= 95
            }
        
        return support
    
    def get_font_info(self) -> dict:
        """Extract font metadata."""
        info = {
            'family': 'Unknown',
            'subfamily': 'Unknown',
            'full_name': 'Unknown',
            'version': 'Unknown',
        }
        
        if 'name' in self.font:
            for record in self.font['name'].names:
                try:
                    text = record.toUnicode()
                    
                    if record.nameID == 1:
                        info['family'] = text
                    elif record.nameID == 2:
                        info['subfamily'] = text
                    elif record.nameID == 4:
                        info['full_name'] = text
                    elif record.nameID == 5:
                        info['version'] = text
                except:
                    continue
        
        return info
    
    def get_statistics(self) -> dict:
        """Get font file statistics."""
        file_size = self.font_path.stat().st_size
        
        # Count tables
        tables = len(self.font.keys())
        
        # Count glyphs
        num_glyphs = len(self.font.getGlyphOrder())
        
        return {
            'file_size_kb': round(file_size / 1024, 1),
            'tables': tables,
            'glyphs': num_glyphs
        }
    
    def print_report(self, verbose: bool = False):
        """Print detailed analysis report."""
        
        print("=" * 70)
        print("🔍 FONT ANALYSIS REPORT")
        print("=" * 70)
        
        # Font info
        info = self.get_font_info()
        print(f"\n📋 Font Information:")
        print(f"   Family: {info['family']}")
        print(f"   Subfamily: {info['subfamily']}")
        print(f"   Full Name: {info['full_name']}")
        print(f"   Version: {info['version']}")
        
        # Statistics
        stats = self.get_statistics()
        print(f"\n📊 Statistics:")
        print(f"   File Size: {stats['file_size_kb']} KB")
        print(f"   Tables: {stats['tables']}")
        print(f"   Glyphs: {stats['glyphs']}")
        
        # Unicode map
        unicode_map = self.get_unicode_map()
        print(f"\n🔤 Character Coverage:")
        print(f"   Total Characters: {len(unicode_map)}")
        
        # Language support
        support = self.check_language_support(unicode_map)
        print(f"\n🌍 Language Support:")
        
        for lang, data in sorted(support.items()):
            status = "✅" if data['supported'] else "⚠️"
            print(f"   {status} {lang}:")
            print(f"      Coverage: {data['coverage']}% ({data['found']}/{data['required']})")
            if data['missing'] > 0 and verbose:
                print(f"      Missing: {data['missing']} characters")
        
        # Unicode blocks
        blocks = self.analyze_blocks(unicode_map)
        print(f"\n📚 Unicode Blocks:")
        
        for block_name, chars in sorted(blocks.items(), key=lambda x: -len(x[1])):
            if len(chars) > 0:
                print(f"   {block_name}: {len(chars)} chars")
                if verbose and len(chars) <= 20:
                    sample = ''.join(c['char'] for c in chars[:50])
                    print(f"      {sample}")
        
        print("\n" + "=" * 70)
        
        return {
            'info': info,
            'stats': stats,
            'unicode_map': unicode_map,
            'blocks': blocks,
            'support': support
        }


def main():
    parser = argparse.ArgumentParser(
        description='Analyze font files for character coverage'
    )
    
    parser.add_argument(
        'font',
        help='Font file to analyze (OTF/TTF/WOFF2)'
    )
    
    parser.add_argument(
        '--verbose', '-v',
        action='store_true',
        help='Show detailed output including all characters'
    )
    
    parser.add_argument(
        '--output', '-o',
        help='Save report to file'
    )
    
    args = parser.parse_args()
    
    try:
        analyzer = FontAnalyzer(args.font)
        report = analyzer.print_report(args.verbose)
        
        if args.output:
            import json
            with open(args.output, 'w', encoding='utf-8') as f:
                json.dump(report, f, indent=2, ensure_ascii=False)
            print(f"\n💾 Report saved to: {args.output}")
        
        return 0
        
    except FileNotFoundError as e:
        print(f"❌ {e}")
        return 1
    except Exception as e:
        print(f"❌ Error analyzing font: {e}")
        return 1


if __name__ == "__main__":
    exit(main())
