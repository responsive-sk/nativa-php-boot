# Font Subsetter

Python tool na vytváranie optimalizovaných web fontov na základe skutočného textového obsahu.

## 🚀 Rýchly štart

```bash
cd font-subsetter
python -m venv venv
./venv/bin/pip install -r requirements.txt
```

## 📁 Štruktúra

```
font-subsetter/
├── fonts/
│   ├── sans-serif/          # Bezpätkové fonty
│   │   ├── Anton-Regular.ttf
│   │   ├── BebasNeue-Regular.ttf
│   │   ├── Lato-Regular.ttf
│   │   ├── OpenSans-Regular.ttf
│   │   ├── Roboto-Regular.ttf
│   │   └── Unbounded-Regular.ttf
│   │
│   └── serif/               # Pätkové fonty
│       ├── CormorantGaramond-Regular.ttf
│       └── PlayfairDisplay-Regular.ttf
│
├── output/
│   ├── sans-serif/          # Spracované bezpätkové
│   │   ├── anton/           → Anton.{ttf,woff,woff2,css}
│   │   ├── bebasneue/
│   │   ├── lato/
│   │   ├── opensans/
│   │   ├── roboto/
│   │   └── unbounded/
│   │
│   └── serif/               # Spracované pätkové
│       ├── cormorantgaramond/
│       └── playfairdisplay/
│
├── font_subsetter.py        # Single font CLI
├── batch_subsetter.py       # Batch processing
├── test_font_subsetter.py   # Testy
└── README.md
```

## 📖 Použitie

### Batch Processing (všetky fonty naraz)

```bash
# Všetky sans-serif fonty
./venv/bin/python batch_subsetter.py \
  --inputs sk-cz-test.txt \
  --fonts fonts/sans-serif/*.ttf \
  --out output/sans-serif

# Všetky serif fonty
./venv/bin/python batch_subsetter.py \
  --inputs sk-cz-test.txt \
  --fonts fonts/serif/*.ttf \
  --out output/serif
```

### Single Font

```bash
./venv/bin/python font_subsetter.py \
  --font fonts/sans-serif/Roboto-Regular.ttf \
  --inputs content/*.html \
  --family MyFont \
  --out output/myfont
```

## 📊 Výsledky

### Sans-serif (Bezpätkové)

| Font | TTF | WOFF2 | Úspora | Použitie |
|------|-----|-------|--------|----------|
| **Anton** | 12.3K | 8.4K | ‑32% | Nadpisy |
| **Bebas Neue** | 12.2K | 7.1K | ‑42% | Titulky |
| **Lato** | 13.1K | 9.5K | ‑28% | Text |
| **OpenSans** | 13.1K | 9.6K | ‑26% | Text |
| **Roboto** | 13.9K | 9.8K | ‑30% | Text |
| **Unbounded** | 19.3K | 11.7K | ‑39% | Display |

### Serif (Pätkové)

| Font | TTF | WOFF2 | Úspora | Použitie |
|------|-----|-------|--------|----------|
| **Cormorant Garamond** | 38.1K | 17.0K | ‑55% | Elegantný text |
| **Playfair Display** | 28.4K | 15.4K | ‑46% | Nadpisy |

## 🇸🇰🇨🇿 SK/CZ Znaková Sada

Všetky fonty sú testované so slovenskými a českými znakmi:

**Veľké:** Á Ä Č Ď É Í Ĺ Ľ Ň Ó Ô Ŕ Š Ť Ú Ý Ž Ě Ř Ů  
**Malé:** á ä č ď é í ľ ĺ ň ó ô ŕ š ť ú ý ž ě ř ů

## 🎨 CSS Usage

```css
/* Sans-serif */
@font-face {
  font-family: 'Roboto';
  src: url('sans-serif/roboto/Roboto.woff2') format('woff2'),
       url('sans-serif/roboto/Roboto.woff') format('woff');
  font-display: swap;
}

/* Serif */
@font-face {
  font-family: 'Playfair Display';
  src: url('serif/playfairdisplay/PlayfairDisplay.woff2') format('woff2'),
       url('serif/playfairdisplay/PlayfairDisplay.woff') format('woff');
  font-display: swap;
}

body {
  font-family: 'Roboto', sans-serif;
}

h1, h2 {
  font-family: 'Playfair Display', serif;
}
```

## 🧪 Testy

```bash
./venv/bin/pip install pytest
./venv/bin/python -m pytest test_font_subsetter.py -v
```

## 📦 Fonty

Všetky fonty sú z [Google Fonts](https://fonts.google.com/) (OFL license):

### Sans-serif
- **Roboto** - Univerzálny, čitateľný
- **Open Sans** - Priateľský, moderný
- **Lato** - Vyvážený, profesionálny
- **Unbounded** - Výrazný, tech
- **Anton** - Bold display
- **Bebas Neue** - Condensed headlines

### Serif
- **Cormorant Garamond** - Klasický, elegantný
- **Playfair Display** - Moderný serif, nadpisy

## 🔧 Dependencies

- **fonttools[zopfli,woff]** - Font manipulation
- **brotli** - WOFF2 compression
- **beautifulsoup4** - HTML parsing

## License

MIT
