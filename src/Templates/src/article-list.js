// Article List - Auto-mounting Svelte Component
import ArticleList from '../svelte/components/ArticleList.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('article-list-container');
    
    if (container) {
        const articles = JSON.parse(container.dataset.articles || '[]');
        
        new ArticleList({
            target: container,
            props: {
                articles: articles,
                searchQuery: ''
            }
        });
        
        console.log('✅ ArticleList mounted');
    }
});

// Export for manual mounting if needed
export default ArticleList;
