"""Tests for font_subsetter module."""

import pathlib
import tempfile
import unittest
from font_subsetter import extract_chars_from_file, extract_text_from_files, TextExtractor


class TestTextExtractor(unittest.TestCase):
    """Test HTML text extraction."""
    
    def test_extract_plain_text(self):
        extractor = TextExtractor()
        extractor.feed("Hello World")
        self.assertEqual(extractor.get_text(), "Hello World")
    
    def test_extract_with_tags(self):
        extractor = TextExtractor()
        extractor.feed("<p>Hello <strong>World</strong></p>")
        self.assertEqual(extractor.get_text(), "Hello World")
    
    def test_ignore_script(self):
        extractor = TextExtractor()
        extractor.feed("<p>Text</p><script>alert('x');</script>")
        self.assertEqual(extractor.get_text(), "Text")
    
    def test_ignore_style(self):
        extractor = TextExtractor()
        extractor.feed("<p>Text</p><style>.class { color: red; }</style>")
        self.assertEqual(extractor.get_text(), "Text")


class TestExtractCharsFromFile(unittest.TestCase):
    """Test character extraction from files."""
    
    def test_txt_file(self):
        with tempfile.NamedTemporaryFile(mode='w', suffix='.txt', delete=False, encoding='utf-8') as f:
            f.write("Ahoj Svet!")
            path = pathlib.Path(f.name)
        
        chars = extract_chars_from_file(path)
        self.assertIn('A', chars)
        self.assertIn('!', chars)
        path.unlink()
    
    def test_html_file(self):
        with tempfile.NamedTemporaryFile(mode='w', suffix='.html', delete=False, encoding='utf-8') as f:
            f.write("<html><body>Hello<strong>World</strong></body></html>")
            path = pathlib.Path(f.name)
        
        chars = extract_chars_from_file(path)
        self.assertIn('H', chars)
        self.assertNotIn('<', chars)
        path.unlink()
    
    def test_utf8_characters(self):
        with tempfile.NamedTemporaryFile(mode='w', suffix='.txt', delete=False, encoding='utf-8') as f:
            f.write("ŠčŘŽžýáíé")
            path = pathlib.Path(f.name)
        
        chars = extract_chars_from_file(path)
        self.assertIn('Š', chars)
        self.assertIn('ž', chars)
        path.unlink()


class TestExtractTextFromFiles(unittest.TestCase):
    """Test batch character extraction."""
    
    def test_multiple_files(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            tmpdir = pathlib.Path(tmpdir)
            
            file1 = tmpdir / "file1.txt"
            file1.write_text("ABC", encoding='utf-8')
            
            file2 = tmpdir / "file2.txt"
            file2.write_text("DEF", encoding='utf-8')
            
            chars = extract_text_from_files([str(file1), str(file2)])
            
            self.assertEqual(len(chars), 6)
            self.assertIn('A', chars)
            self.assertIn('F', chars)
    
    def test_directory_input(self):
        with tempfile.TemporaryDirectory() as tmpdir:
            tmpdir = pathlib.Path(tmpdir)
            
            (tmpdir / "test.txt").write_text("XYZ", encoding='utf-8')
            
            chars = extract_text_from_files([str(tmpdir)])
            
            self.assertIn('X', chars)
    
    def test_sorted_output(self):
        with tempfile.NamedTemporaryFile(mode='w', suffix='.txt', delete=False) as f:
            f.write("CBA")
            path = pathlib.Path(f.name)
        
        chars = extract_text_from_files([str(path)])
        self.assertEqual(chars, "ABC")
        path.unlink()
    
    def test_nonexistent_file(self):
        chars = extract_text_from_files(["/nonexistent/file.txt"])
        self.assertEqual(chars, "")


if __name__ == "__main__":
    unittest.main()
