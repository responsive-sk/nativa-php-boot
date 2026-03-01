# Notification System Documentation

## Overview

The notification system provides a unified way to display success, error, warning, and info messages throughout the application. It replaces traditional PHP HTML alerts with a modern TypeScript-based solution.

## Features

- **Type-safe**: TypeScript implementation with proper type definitions
- **HTMX Integration**: Automatic handling of HTMX responses
- **Multiple Types**: Success, Error, Warning, Info notifications
- **Auto-dismiss**: Optional auto-dismiss functionality
- **Stack Support**: Multiple notifications can be displayed simultaneously
- **Global Access**: Available globally via `window.notifications`

## Architecture

```
src/utils/notifications.ts
├── NotificationManager class
├── Notification interface
├── NotificationType enum
└── HTMX integration
```

## Usage

### Basic Usage

```javascript
// Show notifications
window.notifications.success("Operation completed successfully!");
window.notifications.error("Something went wrong!");
window.notifications.warning("Please check your input");
window.notifications.info("Here is some useful information");

// Clear all notifications
window.notifications.clear();
```

### HTMX Integration

The system automatically handles HTMX responses with JSON format:

```json
{
  "success": true,
  "message": "Form submitted successfully!",
  "data": {...}
}
```

### Manual Notification Creation

```javascript
window.notifications.show({
    type: "success",
    message: "Custom message",
    autoDismiss: true,
    duration: 5000,
});
```

## Configuration

### Notification Types

```typescript
enum NotificationType {
    SUCCESS = "success",
    ERROR = "error",
    WARNING = "warning",
    INFO = "info",
}
```

### Default Options

```typescript
interface NotificationOptions {
    autoDismiss?: boolean; // Default: true
    duration?: number; // Default: 5000ms
    persistent?: boolean; // Default: false
}
```

## CSS Classes

The system uses BEM CSS classes:

```css
.alerts-container          /* Main container */
.alert                    /* Individual notification */
.alert--success           /* Success notification */
.alert--error             /* Error notification */
.alert--warning           /* Warning notification */
.alert--info              /* Info notification */
.alert__close             /* Close button */
.alert--dismiss           /* Dismiss animation */
```

## Integration with HTMX

### Server Response Format

```php
// In your API actions
return $this->responseFactory->createResponse(
    json_encode([
        'success' => true,
        'message' => 'Contact form submitted successfully!'
    ])
)->withHeader('Content-Type', 'application/json');
```

### Auto-detection

The system automatically detects HTMX requests and processes JSON responses:

```typescript
// HTMX event listeners
document.body.addEventListener("htmx:afterRequest", (event) => {
    const xhr = event.detail.xhr;
    const response = JSON.parse(xhr.responseText);

    if (response.success) {
        window.notifications.success(response.message);
    } else {
        window.notifications.error(response.message);
    }
});
```

## Examples

### Contact Form Integration

```javascript
// In your HTMX form
<form hx-post="/api/contact" hx-target="#result">
  <!-- form fields -->
</form>

// Server returns JSON
{
  "success": true,
  "message": "Message sent successfully!"
}

// Auto-displayed notification
```

### Demo Buttons

```html
<button onclick="window.notifications.success('Success!')">Success</button>
<button onclick="window.notifications.error('Error!')">Error</button>
```

## Best Practices

1. **Consistent Messages**: Use consistent message formats
2. **Proper Types**: Choose appropriate notification types
3. **Auto-dismiss**: Use auto-dismiss for non-critical messages
4. **Server Validation**: Validate on server, return proper JSON responses
5. **Error Handling**: Always handle error cases gracefully

## Troubleshooting

### Common Issues

1. **Notifications not showing**: Check if `notifications.ts` is imported
2. **HTMX not working**: Verify JSON response format
3. **CSS issues**: Ensure `.alerts-container` exists in HTML
4. **Type errors**: Check TypeScript compilation

### Debug Mode

```javascript
// Enable debug logging
window.notifications.debug = true;
```

## File Structure

```
views/templates/
├── src/utils/
│   └── notifications.ts          # Main notification system
├── src/styles/components/
│   └── alert.css               # Notification styles
├── pages/
│   ├── contact.php              # Demo buttons
│   └── ...                    # Other pages
└── layouts/
    └── home.php                 # Container in layout
```

## Migration from PHP Alerts

### Before (PHP)

```php
<?php if ($success): ?>
<div class="alert alert-success">
  <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>
```

### After (TypeScript)

```javascript
// In your API response
echo json_encode(['success' => true, 'message' => $success]);

// Auto-displayed notification
```

## Performance

- **Lightweight**: ~2KB minified
- **Zero Dependencies**: Pure TypeScript implementation
- **Fast**: DOM manipulation optimized
- **Memory Efficient**: Automatic cleanup of dismissed notifications

## Accessibility

- **ARIA Labels**: Proper ARIA attributes
- **Keyboard Navigation**: Tab and Enter support
- **Screen Reader**: Compatible with screen readers
- **Color Contrast**: WCAG compliant colors
- **Focus Management**: Proper focus handling

## Browser Support

- **Modern Browsers**: Full support
- **IE11**: Basic functionality
- **Mobile**: Touch-friendly dismiss buttons
