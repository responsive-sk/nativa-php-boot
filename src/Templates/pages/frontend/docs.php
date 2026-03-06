<?php

declare(strict_types = 1);

/**
 * Documentation Page Template.
 *
 * @var string $pageTitle
 */
$pageTitle ??= 'Documentation - Nativa CMS';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <h1 class="hero__title">Documentation</h1>
        <p class="hero__subtitle">Complete guides and API reference for Nativa CMS</p>
        <div class="hero__search">
            <input type="text" class="hero__search-input" placeholder="Search documentation..." id="docsSearch">
        </div>
    </div>
</section>

<!-- Documentation Sections -->
<section class="docs">
    <div class="docs__container">
        <!-- Sidebar Navigation -->
        <aside class="docs__sidebar">
            <nav class="docs__nav">
                <div class="docs__nav-group">
                    <h3 class="docs__nav-title">Getting Started</h3>
                    <ul class="docs__nav-list">
                        <li><a href="#introduction" class="docs__nav-link">Introduction</a></li>
                        <li><a href="#installation" class="docs__nav-link">Installation</a></li>
                        <li><a href="#configuration" class="docs__nav-link">Configuration</a></li>
                        <li><a href="#quickstart" class="docs__nav-link">Quick Start</a></li>
                    </ul>
                </div>

                <div class="docs__nav-group">
                    <h3 class="docs__nav-title">Core Concepts</h3>
                    <ul class="docs__nav-list">
                        <li><a href="#architecture" class="docs__nav-link">Architecture</a></li>
                        <li><a href="#ddd" class="docs__nav-link">Domain-Driven Design</a></li>
                        <li><a href="#value-objects" class="docs__nav-link">Value Objects</a></li>
                        <li><a href="#entities" class="docs__nav-link">Entities</a></li>
                    </ul>
                </div>

                <div class="docs__nav-group">
                    <h3 class="docs__nav-title">API Reference</h3>
                    <ul class="docs__nav-list">
                        <li><a href="#articles" class="docs__nav-link">Articles API</a></li>
                        <li><a href="#pages" class="docs__nav-link">Pages API</a></li>
                        <li><a href="#forms" class="docs__nav-link">Forms API</a></li>
                        <li><a href="#media" class="docs__nav-link">Media API</a></li>
                    </ul>
                </div>

                <div class="docs__nav-group">
                    <h3 class="docs__nav-title">Advanced</h3>
                    <ul class="docs__nav-list">
                        <li><a href="#plugins" class="docs__nav-link">Plugins</a></li>
                        <li><a href="#themes" class="docs__nav-link">Themes</a></li>
                        <li><a href="#deployment" class="docs__nav-link">Deployment</a></li>
                        <li><a href="#performance" class="docs__nav-link">Performance</a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="docs__content">
            <!-- Introduction -->
            <section id="introduction" class="docs__section">
                <div class="docs__header">
                    <h2 class="docs__title">Introduction</h2>
                    <p class="docs__lead">Welcome to Nativa CMS documentation</p>
                </div>

                <div class="docs__body">
                    <p>Nativa is a modern PHP 8.4+ CMS and Blog Platform built with Domain-Driven Design principles.</p>

                    <div class="code-block">
                        <pre><code class="language-bash"># Quick installation
composer create-project nativa/cms
php bin/cms migrate
php bin/cms serve</code></pre>
                    </div>

                    <div class="info-box">
                        <strong>Requirements:</strong>
                        <ul>
                            <li>PHP 8.4+</li>
                            <li>SQLite or MySQL</li>
                            <li>Composer</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Architecture -->
            <section id="architecture" class="docs__section">
                <div class="docs__header">
                    <h2 class="docs__title">Architecture</h2>
                    <p class="docs__lead">Four-layer DDD architecture</p>
                </div>

                <div class="docs__body">
                    <p>Nativa follows strict Domain-Driven Design with clear separation of concerns:</p>

                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-card__icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                    <path d="M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <h3 class="feature-card__title">Domain Layer</h3>
                            <p class="feature-card__desc">Pure business logic, entities, value objects</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-card__icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </div>
                            <h3 class="feature-card__title">Application Layer</h3>
                            <p class="feature-card__desc">Use cases, services, DTOs</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-card__icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                                </svg>
                            </div>
                            <h3 class="feature-card__title">Infrastructure Layer</h3>
                            <p class="feature-card__desc">Database, storage, external services</p>
                        </div>

                        <div class="feature-card">
                            <div class="feature-card__icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M2 12h20"/>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                            </div>
                            <h3 class="feature-card__title">Interfaces Layer</h3>
                            <p class="feature-card__desc">HTTP controllers, CLI, templates</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Code Example -->
            <section id="quickstart" class="docs__section">
                <div class="docs__header">
                    <h2 class="docs__title">Quick Start</h2>
                    <p class="docs__lead">Create your first article in minutes</p>
                </div>

                <div class="docs__body">
                    <div class="steps">
                        <div class="step">
                            <span class="step__number">1</span>
                            <div class="step__content">
                                <h4 class="step__title">Install Nativa</h4>
                                <div class="code-block">
                                    <pre><code class="language-bash">composer create-project nativa/cms my-blog
cd my-blog</code></pre>
                                </div>
                            </div>
                        </div>

                        <div class="step">
                            <span class="step__number">2</span>
                            <div class="step__content">
                                <h4 class="step__title">Initialize Database</h4>
                                <div class="code-block">
                                    <pre><code class="language-bash">php bin/cms migrate
php bin/cms seed</code></pre>
                                </div>
                            </div>
                        </div>

                        <div class="step">
                            <span class="step__number">3</span>
                            <div class="step__content">
                                <h4 class="step__title">Start Development Server</h4>
                                <div class="code-block">
                                    <pre><code class="language-bash">php bin/cms serve</code></pre>
                                </div>
                                <p class="step__desc">Server running at <code>http://localhost:8000</code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- API Example -->
            <section id="articles" class="docs__section">
                <div class="docs__header">
                    <h2 class="docs__title">Articles API</h2>
                    <p class="docs__lead">Working with articles programmatically</p>
                </div>

                <div class="docs__body">
                    <p>Use the ArticleManager service to create and manage articles:</p>

                    <div class="code-block">
                        <pre><code class="language-php">&lt;?php

use Application\Services\ArticleManager;
use Domain\Model\Article;

$articleManager = new ArticleManager($repository, $unitOfWork);

// Create article
$article = $articleManager->create(
    authorId: 'user-123',
    title: 'My First Post',
    content: 'Hello world!',
    excerpt: 'Welcome to my blog'
);

// Publish article
$articleManager->publish($article->id());</code></pre>
                    </div>

                    <div class="warning-box">
                        <strong>Note:</strong> Always wrap write operations in transactions using UnitOfWork.
                    </div>
                </div>
            </section>
        </main>
    </div>
</section>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>
