# 🎨 Font Generator & Optimizer

Creates optimized font subsets for **Slovak (SK)**, **Czech (CZ)**, and **English (EN)** languages.

## 🚀 Quick Start

```bash
cd FontGenerator

# 1. Place your input font (OTF or TTF)
cp /path/to/your/font.otf input.otf

# 2. Generate optimized fonts
python3 create_font_from_html.py

# 3. Test in browser
open font-test.html
```

## 📦 Features

### Character Sets

#### Slovak (SK)
- **sk-minimal** - Basic letters only (~5-10KB)
- **sk-basic** - + numbers & punctuation (~10-15KB)
- **sk-web** - Complete web set (~15-25KB) ⭐ Recommended
- **sk-full** - All special characters (~25-35KB)

#### Czech (CZ)
- **cz-minimal** - Basic letters only (~5-10KB)
- **cz-basic** - + numbers & punctuation (~10-15KB)
- **cz-web** - Complete web set (~15-25KB) ⭐ Recommended
- **cz-full** - All special characters (~25-35KB)

#### English (EN)
- **en-minimal** - A-Z, a-z only (~3-8KB)
- **en-basic** - + numbers & punctuation (~8-12KB)
- **en-web** - Complete web set (~12-20KB) ⭐ Recommended
- **en-full** - All special characters (~20-30KB)

#### Special Purpose
- **numbers-only** - Just digits for counters/prices
- **ui-minimal** - For buttons, labels, navigation
- **heading-bold** - Optimized for headlines

## 📖 Usage

### Generate All Language Sets

```bash
python3 create_font_from_html.py
```

### Generate Only Slovak Fonts

```bash
python3 create_font_from_html.py --sk-only
```

### Generate Only Web-Optimized Sets

```bash
python3 create_font_from_html.py --web-only
```

### Analyze HTML First

```bash
python3 create_font_from_html.py --analyze index.html
```

### Use Custom Input Font

```bash
python3 create_font_from_html.py --input MyFont.otf
```

### Analyze Existing Font

```bash
python3 analyze_font.py fonts/font-sk-web.woff2 --verbose
```

## 📁 Output Structure

```
FontGenerator/
├── fonts/
│   ├── font-sk-minimal.woff2
│   ├── font-sk-web.woff2
│   ├── font-cz-minimal.woff2
│   ├── font-cz-web.woff2
│   ├── font-en-minimal.woff2
│   ├── font-en-web.woff2
│   ├── font-ui-minimal.woff2
│   ├── font-numbers-only.woff2
│   └── styles.css
├── font-test.html
├── create_font_from_html.py
├── analyze_font.py
└── README.md
```

## 🎯 Usage Examples

### CSS

```css
/* Import generated fonts */
@font-face {
  font-family: 'font-sk-web';
  src: url('fonts/font-sk-web.woff2') format('woff2');
  font-display: swap;
}

/* Use language-specific fonts */
.sk-text {
  font-family: 'font-sk-web', sans-serif;
}

.cz-text {
  font-family: 'font-cz-web', sans-serif;
}

/* Use for specific purposes */
.ui-button {
  font-family: 'font-ui-minimal', sans-serif;
}

.price {
  font-family: 'font-numbers-only', monospace;
}
```

### HTML

```html
<!DOCTYPE html>
<html lang="sk">
<head>
  <link rel="stylesheet" href="fonts/styles.css">
</head>
<body>
  <!-- Slovak content -->
  <p class="lang-sk">Vitajte na našej stránke!</p>
  
  <!-- Czech content -->
  <p class="lang-cz">Vítejte na našich stránkách!</p>
  
  <!-- English content -->
  <p class="lang-en">Welcome to our website!</p>
  
  <!-- UI elements -->
  <button class="ui-text">Click Me</button>
  
  <!-- Prices -->
  <span class="numbers-only">99.99€</span>
</body>
</html>
```

## 🔧 Requirements

```bash
pip install fonttools brotli
```

## 📊 Size Comparison

| Font Set | Characters | Size | Use Case |
|----------|-----------|------|----------|
| sk-minimal | ~60 | 5-10KB | UI, buttons |
| sk-web | ~150 | 15-25KB | Articles, blogs ⭐ |
| sk-full | ~300 | 25-35KB | Rich content |
| en-minimal | ~52 | 3-8KB | Basic English |
| en-web | ~100 | 12-20KB | English sites ⭐ |

## 🎯 Benefits

✅ **Smaller file sizes** - 60-80% smaller than full fonts  
✅ **Faster page loads** - Less data to download  
✅ **Better performance** - Faster font rendering  
✅ **Language optimized** - Only characters you need  
✅ **Unicode-range ready** - Automatic language switching  

## 🧪 Testing

Open `font-test.html` in your browser to:
- Preview all generated fonts
- Test character coverage
- See size comparisons
- Copy CSS code snippets

## 📝 License

This tool is provided as-is. Fonts are subject to their respective licenses.

## 🤝 Contributing

Feel free to add more language sets or improve character coverage!
