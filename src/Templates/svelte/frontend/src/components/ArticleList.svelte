<script>
    let { articles = [], searchQuery = '' } = $props();

    // Reactive filter (Svelte 5 $derived)
    let filteredArticles = $derived(
        articles.filter(article =>
            !searchQuery ||
            article.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
            article.excerpt.toLowerCase().includes(searchQuery.toLowerCase())
        )
    );

    function handleSearch(event) {
        searchQuery = event.target.value;
    }
</script>

<div class="article-list-svelte">
    {#if searchQuery}
        <div class="search-info">
            Showing {filteredArticles.length} results for "{searchQuery}"
        </div>
    {/if}
    
    {#each filteredArticles as article (article.id)}
        <article class="article-card" data-aos="fade-up">
            <header>
                <h2>
                    <a href="/blog/{article.slug}">{article.title}</a>
                </h2>
                <time datetime={article.publishedAt}>
                    {new Date(article.publishedAt).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    })}
                </time>
            </header>
            
            <p class="excerpt">{article.excerpt}</p>
            
            <footer>
                {#if article.tags && article.tags.length > 0}
                    <div class="tags">
                        {#each article.tags as tag (tag)}
                            <span class="tag">#{tag}</span>
                        {/each}
                    </div>
                {/if}
                
                <a href="/blog/{article.slug}" class="read-more">
                    Read more →
                </a>
            </footer>
        </article>
    {/each}
    
    {#if filteredArticles.length === 0}
        <div class="no-results">
            <p>No articles found{searchQuery ? ` for "${searchQuery}"` : ''}.</p>
        </div>
    {/if}
</div>

<style>
    .article-list-svelte {
        display: grid;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .article-card {
        padding: 2rem;
        border-radius: 12px;
        background: var(--card-bg, #fff);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .article-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }
    
    .article-card header {
        margin-bottom: 1rem;
    }
    
    .article-card h2 {
        margin: 0 0 0.5rem 0;
        font-size: 1.5rem;
    }
    
    .article-card h2 a {
        color: var(--text-primary, #1a1a1a);
        text-decoration: none;
    }
    
    .article-card h2 a:hover {
        color: var(--accent, #007bff);
    }
    
    .article-card time {
        color: var(--text-secondary, #666);
        font-size: 0.875rem;
    }
    
    .excerpt {
        color: var(--text-secondary, #4a4a4a);
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    
    .tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    
    .tag {
        padding: 0.25rem 0.75rem;
        background: var(--tag-bg, #e0e0e0);
        border-radius: 9999px;
        font-size: 0.875rem;
        color: var(--tag-text, #333);
    }
    
    .read-more {
        color: var(--accent, #007bff);
        text-decoration: none;
        font-weight: 500;
    }
    
    .read-more:hover {
        text-decoration: underline;
    }
    
    .search-info {
        padding: 1rem;
        background: var(--info-bg, #e3f2fd);
        border-radius: 8px;
        margin-bottom: 1rem;
        color: var(--info-text, #1976d2);
    }
    
    .no-results {
        text-align: center;
        padding: 3rem;
        color: var(--text-secondary, #666);
    }
</style>
