<script>
    export let actionUrl = '/contact';
    export let csrfToken = '';
    
    let name = '';
    let email = '';
    let message = '';
    let subject = '';
    
    let submitting = false;
    let success = false;
    let error = null;
    let errors = {};
    
    function validate() {
        errors = {};
        
        if (!name.trim()) errors.name = 'Name is required';
        if (!email.trim()) {
            errors.email = 'Email is required';
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.email = 'Invalid email format';
        }
        if (!message.trim()) errors.message = 'Message is required';
        
        return Object.keys(errors).length === 0;
    }
    
    async function handleSubmit(event) {
        event.preventDefault();
        
        if (!validate()) return;
        
        submitting = true;
        error = null;
        errors = {};
        
        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('message', message);
            formData.append('subject', subject);
            formData.append('_csrf_token', csrfToken);
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (response.ok) {
                success = true;
                name = '';
                email = '';
                message = '';
                subject = '';
            } else {
                error = data.error || 'Something went wrong';
            }
        } catch (e) {
            error = 'Network error. Please try again.';
        } finally {
            submitting = false;
        }
    }
</script>

<form class="contact-form-svelte" on:submit={handleSubmit} novalidate>
    {#if success}
        <div class="success-message">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-width="2"/>
                <polyline points="22 4 12 14.01 9 11.01" stroke-width="2"/>
            </svg>
            <h3>Thank you!</h3>
            <p>Your message has been sent successfully.</p>
        </div>
    {:else}
        {#if error}
            <div class="error-message global">
                {error}
            </div>
        {/if}
        
        <div class="form-group">
            <label for="name">Name *</label>
            <input
                id="name"
                type="text"
                bind:value={name}
                class:error={errors.name}
                required
            >
            {#if errors.name}
                <span class="error-text">{errors.name}</span>
            {/if}
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input
                id="email"
                type="email"
                bind:value={email}
                class:error={errors.email}
                required
            >
            {#if errors.email}
                <span class="error-text">{errors.email}</span>
            {/if}
        </div>
        
        <div class="form-group">
            <label for="subject">Subject</label>
            <input
                id="subject"
                type="text"
                bind:value={subject}
            >
        </div>
        
        <div class="form-group">
            <label for="message">Message *</label>
            <textarea
                id="message"
                bind:value={message}
                rows="5"
                class:error={errors.message}
                required
            ></textarea>
            {#if errors.message}
                <span class="error-text">{errors.message}</span>
            {/if}
        </div>
        
        <input type="hidden" name="_csrf_token" value={csrfToken}>
        
        <button type="submit" class="submit-btn" disabled={submitting}>
            {#if submitting}
                <span class="spinner"></span>
                Sending...
            {:else}
                Send Message
            {/if}
        </button>
    {/if}
</form>

<style>
    .contact-form-svelte {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-primary, #1a1a1a);
    }
    
    input,
    textarea {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid var(--border, #e0e0e0);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--input-bg, #fff);
        color: var(--text-primary, #1a1a1a);
        transition: border-color 0.3s ease;
    }
    
    input:focus,
    textarea:focus {
        outline: none;
        border-color: var(--accent, #007bff);
    }
    
    input.error,
    textarea.error {
        border-color: var(--error, #dc3545);
    }
    
    .error-text {
        display: block;
        margin-top: 0.25rem;
        color: var(--error, #dc3545);
        font-size: 0.875rem;
    }
    
    .submit-btn {
        width: 100%;
        padding: 1rem;
        background: var(--accent, #007bff);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .submit-btn:hover:not(:disabled) {
        background: var(--accent-hover, #0056b3);
    }
    
    .submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .spinner {
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .success-message {
        text-align: center;
        padding: 2rem;
        background: var(--success-bg, #d4edda);
        border-radius: 12px;
        color: var(--success-text, #155724);
    }
    
    .success-message .icon {
        width: 3rem;
        height: 3rem;
        margin: 0 auto 1rem;
        color: var(--success, #28a745);
    }
    
    .success-message h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1.5rem;
    }
    
    .error-message.global {
        padding: 1rem;
        background: var(--error-bg, #f8d7da);
        border-radius: 8px;
        color: var(--error-text, #721c24);
        margin-bottom: 1rem;
    }
</style>
