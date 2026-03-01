<?php

declare(strict_types=1);

/**
 * Contact Template - CMS Integration
 *
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

// Cloudinary hero images
$contactHeroImageMobile = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_768/v1658528026/cld-sample-4.jpg';
$contactHeroImageDesktop = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_1280/v1658528026/cld-sample-4.jpg';

// Debug logging
error_log("DEBUG: contact.php template rendering - page: {$page}, title: {$pageTitle}");
?>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="contact-hero__overlay"></div>
    <picture class="contact-hero__picture">
        <source media="(min-width: 769px)" srcset="<?= $contactHeroImageDesktop ?>" crossorigin="anonymous">
        <img src="<?= $contactHeroImageMobile ?>" alt="Contact background" fetchpriority="high" loading="eager" decoding="async" class="contact-hero__image" width="1280" height="720" crossorigin="anonymous">
    </picture>
    <div class="contact-hero__content">
        <h1>Get In Touch</h1>
        <p>Have a project in mind? Let's discuss how we can help you.</p>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact">
  <div class="container">
    <h2>Send us a message</h2>
    <p>We'd love to hear from you. Fill out the form below and we'll get back to you as soon as possible.</p>
    
    <div class="contact__grid">
    <div class="contact__info">
      <div class="contact__info-item">
        <div class="contact__info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <h3>Address</h3>
        <p>123 Business Street<br> Bratislava, Slovakia</p>
      </div>
      <div class="contact__info-item">
        <div class="contact__info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M22 10h-6"/>
            <path d="M2 10h6"/>
            <path d="M12 2v8"/>
            <path d="M12 22v-8"/>
          </svg>
        </div>
        <h3>Email</h3>
        <p>hello@app.dev</p>
      </div>
      <div class="contact__info-item">
        <div class="contact__info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="5" y="2" width="14" height="20" rx="2"/>
            <line x1="12" y1="18" x2="12.01" y2="18"/>
          </svg>
        </div>
        <h3>Phone</h3>
        <p>+421 123 456 789</p>
      </div>
    </div>

    <div class="contact__form-container">
      <!-- Loading indicator -->
      <div id="contact-loading" class="htmx-indicator" style="display: none;">
        Sending...
      </div>
      
      <!-- Success/Error messages -->
      <div id="contact-message"></div>
      
      <!-- Contact form with HTMX -->
      <form 
        class="contact__form" 
        hx-post="/api/contact" 
        hx-target="#contact-message" 
        hx-indicator="#contact-loading"
        hx-swap="innerHTML"
      >
        <input type="hidden" name="_csrf-frontend" value="">
        <div class="contact__form-group">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="contact__form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="contact__form-group">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" required>
        </div>
        <div class="contact__form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn--primary">Send Message</button>
      </form>
    </div>
  </div>
</section>
