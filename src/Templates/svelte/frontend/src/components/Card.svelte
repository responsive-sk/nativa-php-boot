<script>
    import { flip, scale } from 'svelte/transition';
    
    export let title = '';
    export let excerpt = '';
    export let href = '#';
    export let image = null;
    export let tags = [];
    export let date = null;
    export let animate = true;
    
    function formatDate(dateString) {
        if (!dateString) return '';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
</script>

<article 
    class="card-svelte"
    {animate}
    in:flip={{ duration: 400 }}
    in:scale={{ duration: 300 }}
>
    {#if image}
        <div class="card-image">
            <img src={image} alt={title} loading="lazy">
        </div>
    {/if}
    
    <div class="card-content">
        <header class="card-header">
            <h3 class="card-title">
                <a href={href}>{title}</a>
            </h3>
            
            {#if date}
                <time class="card-date" datetime={date}>
                    {formatDate(date)}
                </time>
            {/if}
        </header>
        
        {#if excerpt}
            <p class="card-excerpt">{excerpt}</p>
        {/if}
        
        {#if tags && tags.length > 0}
            <div class="card-tags">
                {#each tags as tag (tag)}
                    <span class="tag">#{tag}</span>
                {/each}
            </div>
        {/if}
        
        <footer class="card-footer">
            <a href={href} class="card-link">
                Read more →
            </a>
        </footer>
    </div>
</article>

<style>
    .card-svelte {
        display: flex;
        flex-direction: column;
        background: var(--card-bg, #fff);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card-svelte:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
    }
    
    .card-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }
    
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .card-svelte:hover .card-image img {
        transform: scale(1.05);
    }
    
    .card-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .card-header {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .card-title {
        margin: 0;
        font-size: 1.25rem;
    }
    
    .card-title a {
        color: var(--text-primary, #1a1a1a);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .card-title a:hover {
        color: var(--accent, #007bff);
    }
    
    .card-date {
        color: var(--text-secondary, #666);
        font-size: 0.875rem;
    }
    
    .card-excerpt {
        color: var(--text-secondary, #4a4a4a);
        line-height: 1.6;
        margin: 0;
    }
    
    .card-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .tag {
        padding: 0.25rem 0.75rem;
        background: var(--tag-bg, #e0e0e0);
        border-radius: 9999px;
        font-size: 0.875rem;
        color: var(--tag-text, #333);
    }
    
    .card-footer {
        margin-top: auto;
    }
    
    .card-link {
        color: var(--accent, #007bff);
        text-decoration: none;
        font-weight: 500;
        transition: text-decoration 0.3s ease;
    }
    
    .card-link:hover {
        text-decoration: underline;
    }
</style>
