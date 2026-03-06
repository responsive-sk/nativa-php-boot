// Contact Form - Auto-mounting Svelte Component
import ContactForm from '../svelte/components/ContactForm.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('contact-form-container');
    
    if (container) {
        // Get CSRF token from meta tag or data attribute
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                          document.getElementById('csrf-token')?.dataset.token || '';
        
        new ContactForm({
            target: container,
            props: {
                actionUrl: '/contact',
                csrfToken: csrfToken
            }
        });
        
        console.log('✅ ContactForm mounted');
    }
});

// Export for manual mounting if needed
export default ContactForm;
