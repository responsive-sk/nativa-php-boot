# Articles Listing & Seeding Plan

## Tasks

### 1. Create Articles Listing Page (`/articles`)
**File:** `Templates/pages/frontend/articles.php`
**Time:** 20 min

**Requirements:**
- Grid layout with ArticleCard components
- Pagination support
- Search functionality
- Empty state handling
- Match blog.php style

**Template Structure:**
```php
<section class="articles-hero">
  <h1>All Articles</h1>
  <p>Latest insights and tutorials</p>
</section>

<section class="articles">
  <div class="articles-search">
    <!-- Search form -->
  </div>
  
  <div id="articles-results">
    <!-- Articles loaded via JS -->
  </div>
  
  <!-- Pagination -->
</section>

<script type="module">
  // Load from /api/articles
  // Similar to blog.js
</script>
```

---

### 2. Seed Demo Articles
**File:** `bin/cms seed:articles`
**Time:** 30 min

**Articles to Create:**

#### Article 1: TypeScript Type Generation
```
Title: "End-to-End Type Safety: PHP to TypeScript"
Slug: "end-to-end-type-safety-php-typescript"
Excerpt: "Auto-generate TypeScript types from PHP entities for complete type safety"
Content: [Detailed article about our type generation system]
Tags: ["TypeScript", "PHP", "Type Safety"]
```

#### Article 2: PHP 8.4 Enums
```
Title: "Modern PHP: Using Enums for Better Type Safety"
Slug: "modern-php-enums-type-safety"
Excerpt: "Migrate from class-based value objects to native PHP 8.4 enums"
Content: [Guide on enum migration]
Tags: ["PHP 8.4", "Enums", "Best Practices"]
```

#### Article 3: Templates Root Migration
```
Title: "Restructuring Templates for Better Organization"
Slug: "restructuring-templates-better-organization"
Excerpt: "Move from nested paths to centralized Templates root"
Content: [Migration guide]
Tags: ["Architecture", "Templates", "Refactoring"]
```

#### Article 4: ArticleCard Component
```
Title: "Building Reusable TypeScript Components"
Slug: "building-reusable-typescript-components"
Excerpt: "Create type-safe UI components with generated types"
Content: [Component development guide]
Tags: ["TypeScript", "Components", "Frontend"]
```

#### Article 5: AssetHelper & Vite
```
Title: "Asset Management with Vite and PHP"
Slug: "asset-management-vite-php"
Excerpt: "Handle hashed assets and manifest with AssetHelper"
Content: [Asset pipeline guide]
Tags: ["Vite", "Assets", "Build Tools"]
```

---

### 3. Update Admin Panel
**File:** `Templates/pages/admin/articles.php`
**Time:** 15 min

**Requirements:**
- Link to new articles page
- Show article count
- Quick actions

---

## Implementation Order

1. ✅ Create seed script (immediate gratification)
2. ✅ Create articles page template
3. ✅ Add route and controller
4. ✅ Update admin panel

---

## Seed Command

```bash
php bin/cms seed:articles
```

**Creates:** 5 demo articles about our recent work

---

## Estimated Time
- Seed Script: 30 min
- Articles Page: 20 min
- Admin Update: 15 min
- **Total:** ~1 hour
