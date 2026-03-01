<?php

declare(strict_types=1);
?>

<section class="pricing">
  <div class="pricing__hero">
    <h2>Simple & Transparent Pricing</h2>
    <p>Choose the plan that fits your needs. All plans include a 14-day free trial.</p>
  </div>

  <div class="pricing__toggle">
    <span class="pricing__toggle-label pricing__toggle-label--active" data-billing="monthly">Monthly</span>
    <button class="pricing__toggle-switch" id="billingToggle" data-active="false" aria-label="Toggle billing cycle"></button>
    <span class="pricing__toggle-label" data-billing="annual">Annual</span>
  </div>

  <div class="pricing__grid">
    <article class="pricing-card">
      <h3 class="pricing-card__name">Starter</h3>
      <p class="pricing-card__description">Perfect for small projects</p>
      <div class="pricing-card__price">
        <span class="pricing-card__amount" data-monthly="$29" data-annual="$24">$29</span>
        <span class="pricing-card__period">/month</span>
      </div>
      <ul class="pricing-card__features">
        <li>5 Projects</li>
        <li>10GB Storage</li>
        <li>Basic Analytics</li>
        <li>Email Support</li>
        <li>API Access</li>
      </ul>
      <a href="/contact" class="btn btn--outline pricing-card__cta">Get Started</a>
    </article>

    <article class="pricing-card pricing-card--featured">
      <span class="pricing-card__badge">Most Popular</span>
      <h3 class="pricing-card__name">Professional</h3>
      <p class="pricing-card__description">For growing businesses</p>
      <div class="pricing-card__price">
        <span class="pricing-card__amount" data-monthly="$79" data-annual="$65">$79</span>
        <span class="pricing-card__period">/month</span>
      </div>
      <ul class="pricing-card__features">
        <li>Unlimited Projects</li>
        <li>100GB Storage</li>
        <li>Advanced Analytics</li>
        <li>Priority Support</li>
        <li>API Access</li>
        <li>Custom Domain</li>
        <li>Team Collaboration</li>
      </ul>
      <a href="/contact" class="btn btn--primary pricing-card__cta">Get Started</a>
    </article>

    <article class="pricing-card">
      <h3 class="pricing-card__name">Enterprise</h3>
      <p class="pricing-card__description">For large organizations</p>
      <div class="pricing-card__price">
        <span class="pricing-card__amount" data-monthly="$199" data-annual="$165">$199</span>
        <span class="pricing-card__period">/month</span>
      </div>
      <ul class="pricing-card__features">
        <li>Unlimited Everything</li>
        <li>1TB Storage</li>
        <li>Real-time Analytics</li>
        <li>24/7 Phone Support</li>
        <li>API Access</li>
        <li>Custom Domain</li>
        <li>Team Collaboration</li>
        <li>SSO Integration</li>
        <li>Dedicated Manager</li>
      </ul>
      <a href="/contact" class="btn btn--outline pricing-card__cta">Get Started</a>
    </article>
  </div>

  <div class="pricing__guarantee">
    <div class="pricing__guarantee-icon">🛡️</div>
    <h3 class="pricing__guarantee-title">30-Day Money-Back Guarantee</h3>
    <p class="pricing__guarantee-text">Try risk-free. If you're not satisfied, get a full refund within 30 days.</p>
  </div>

  <div class="pricing__faq">
    <h2>Frequently Asked Questions</h2>
    <div class="pricing__faq-list">
      <div class="faq-item">
        <div class="faq-item__question">
          <span>Can I change plans later?</span>
          <span class="faq-item__icon">+</span>
        </div>
        <div class="faq-item__answer">
          <p>Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate any differences in billing.</p>
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-item__question">
          <span>What payment methods do you accept?</span>
          <span class="faq-item__icon">+</span>
        </div>
        <div class="faq-item__answer">
          <p>We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers for annual plans.</p>
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-item__question">
          <span>Is there a free trial?</span>
          <span class="faq-item__icon">+</span>
        </div>
        <div class="faq-item__answer">
          <p>Yes! All plans come with a 14-day free trial. No credit card required. You can explore all features before committing.</p>
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-item__question">
          <span>What happens if I exceed my plan limits?</span>
          <span class="faq-item__icon">+</span>
        </div>
        <div class="faq-item__answer">
          <p>We'll notify you when you're approaching your limits. You can upgrade your plan or pay for additional usage. We never cut off your service without warning.</p>
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-item__question">
          <span>Do you offer refunds?</span>
          <span class="faq-item__icon">+</span>
        </div>
        <div class="faq-item__answer">
          <p>Yes, we offer a 30-day money-back guarantee. If you're not satisfied, contact us within 30 days for a full refund.</p>
        </div>
      </div>
    </div>
  </div>
</section>
