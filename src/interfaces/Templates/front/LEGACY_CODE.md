# Legacy Code Directory
# Old files that are not used in new architecture

## Safe to Delete

### components/ag/ - Lit-based components (not integrated)
- Card/, styles/, utils/, types/
- These were never used in the main app

### components/sections/ - Old JS section components
- pricing-section.js
- services-section.js

### components/ui/ - Old UI components  
- footer.js
- header.js
- logos/

### shared/sections/ - Old section components (14 files)
- All unused, sections are now in PHP templates

### use-cases/home/sections/ - Empty/nested sections
- Check if any CSS is referenced

## Keep (Used Files)

### Entry Points
- app.ts, css.ts, init.js
- home.ts, blog.ts, contact.ts, docs.ts, portfolio.ts, pricing.ts, services.ts

### Core Architecture
- core/, storage/, ui/, effects/, navigation/, forms/

### Styles
- styles/tokens.css, styles/utilities.css
- styles/components/*.css (all used)
- styles/*-page.css (used in templates)

### Use-case CSS
- use-cases/*/*.css (all used by page-specific bundles)

### Assets
- assets/fonts/, assets/images/ (used in templates)
