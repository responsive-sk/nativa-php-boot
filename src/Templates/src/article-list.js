// Article List - Direct Svelte 5 mount
import { mount } from 'svelte';
import ArticleList from '../svelte/components/ArticleList.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('article-list-container');

    if (container) {
        const articles = JSON.parse(container.dataset.articles || '[]');

        mount(ArticleList, {
            target: container,
            props: {
                articles: articles,
                searchQuery: ''
            }
        });

        console.log('✅ ArticleList mounted');
    }
});
