/**
 * Anim Block - Text Reveal Animation
 * 
 * IntersectionObserver-based text reveal animation
 * Adds .is-visible class when element enters viewport
 */

/**
 * Initialize anim block animations
 */
export function initAnimBlocks(): void {
  const animBlocks = document.querySelectorAll<HTMLElement>('.anim-block');
  
  if (animBlocks.length === 0) {
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        // Optional: unobserve after first trigger for one-time animation
        // observer.unobserve(entry.target);
      } else {
        // Remove class when out of viewport (for repeatable animation)
        // Comment out if you want one-time reveal only
        entry.target.classList.remove('is-visible');
      }
    });
  }, {
    threshold: 0.15, // Trigger when 15% of element is visible
    rootMargin: '0px 0px -50px 0px', // Slight offset for better timing
  });

  animBlocks.forEach((block) => observer.observe(block));
  
  console.log('%c🎬 ANIM BLOCKS READY', 'color: #c8a96e; font-weight: bold', `${animBlocks.length} blocks initialized`);
}
