// Svelte Components Entry Point
// Import and re-export Svelte components

import * as ArticleListModule from '../svelte/components/ArticleList.svelte';
import * as ContactFormModule from '../svelte/components/ContactForm.svelte';
import * as ThemeToggleModule from '../svelte/components/ThemeToggle.svelte';
import * as ThemeToggleSimpleModule from '../svelte/components/ThemeToggleSimple.svelte';

// Svelte 5 exports the component constructor directly
export const ArticleList = ArticleListModule.default || ArticleListModule.ArticleList;
export const ContactForm = ContactFormModule.default || ContactFormModule.ContactForm;
export const ThemeToggle = ThemeToggleModule.default || ThemeToggleModule.ThemeToggle;
export const ThemeToggleSimple = ThemeToggleSimpleModule.default || ThemeToggleSimpleModule.ThemeToggleSimple;
