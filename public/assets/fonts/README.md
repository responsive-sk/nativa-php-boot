# Nativa Fonts

Optimized web fonts for EN+SK+CZ character sets.

## Structure

```
fonts/
├── sans-serif/
│   ├── plein-variable.woff2         (18K) - Primary body text
│   ├── plein-variable-italic.woff2  (19K) - Body text italic
│   ├── open-sans-regular.woff2      (14K) - Alternative body
│   ├── open-sans-bold.woff2         (14K) - Alternative bold
│   ├── alpino-variable.woff2        (22K) - Headlines & accents
│   └── excon-variable.woff2         (18K) - Display & CTAs
│
└── serif/
    ├── playfair-display-regular.woff2 (31K) - Elegant headlines
    ├── playfair-display-bold.woff2    (33K) - Bold headlines
    └── abril-fatface.woff2            (14K) - Dramatic accents
```

## Font Usage

### Primary Stack (Default)
```css
body {
  font-family: 'Plein', 'OpenSans', sans-serif;
}

h1, h2, h3 {
  font-family: 'Playfair Display', serif;
}

.display, .hero__title {
  font-family: 'Excon', 'Alpino', sans-serif;
}
```

### Font Pairs

| Use Case | Primary | Secondary |
|----------|---------|-----------|
| **Body Text** | Plein | OpenSans |
| **Headlines** | Playfair Display | Excon |
| **Accents** | Alpino | Abril Fatface |
| **UI/Nav** | Plein | Inter |

## Optimization

All fonts are subsetted for **EN+SK+CZ** characters only:
- **Average savings:** 30-55% smaller than originals
- **Character support:** Full Slovak & Czech
- **Missing:** Only extremely rare characters (e.g., ĺ in some fonts)

## License

All fonts are from **Google Fonts** (OFL License) - free for commercial use.

## Build

Fonts are copied from `font-subsetter/output/` during build:

```bash
# Source optimized fonts
cp font-subsetter/output/sans-serif/*/sans-serif-*.woff2 src/Templates/src/assets/fonts/sans-serif/
cp font-subsetter/output/serif/*/serif-*.woff2 src/Templates/src/assets/fonts/serif/

# Vite builds to public/assets/fonts/
npm run build
```
