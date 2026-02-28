# Security Audit Tasks - Nativa PHP CMS

**Audit Date:** 2026-02-28  
**Overall Score:** 5.1/10 ⚠️ **Not Production Ready**  
**Auditor:** Security Checklist Skill (OWASP Top 10)

---

## 🔴 CRITICAL Priority (Fix Immediately)

### Task 1: Implement CSRF Protection
**ID:** `SEC-001`  
**Severity:** 🔴 Critical  
**Estimated Time:** 4 hours  

**Problem:**
- No CSRF token validation on any forms (admin + frontend)
- Attackers can trick authenticated users into unintended actions
- Code removes `_token` from form data but never validates it

**Files to Modify:**
- `src/application/Middleware/CsrfMiddleware.php` (new)
- `src/interfaces/HTTP/Kernel.php`
- `src/interfaces/HTTP/Actions/Frontend/DisplayFormAction.php`
- `src/interfaces/Templates/frontend/partials/csrf_token.php` (new)
- All admin form templates

**Implementation:**
```php
// 1. Create CsrfMiddleware
class CsrfMiddleware {
    public function handle(Request $request, callable $next) {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $token = $request->request->get('_token');
            if (!$this->validateToken($token)) {
                throw new CsrfException('Invalid CSRF token', 403);
            }
        }
        return $next($request);
    }
    
    public static function generateToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

// 2. Add token to all forms
<input type="hidden" name="_token" value="<?= $this->e(\Application\Middleware\CsrfMiddleware::generateToken()) ?>">

// 3. Register middleware in Kernel
```

**Acceptance Criteria:**
- [ ] CSRF token generated per session
- [ ] All POST/PUT/DELETE requests validated
- [ ] Token included in all forms (admin + frontend)
- [ ] AJAX requests include token in header
- [ ] Invalid token returns 403 error

---

### Task 2: Implement Rate Limiting
**ID:** `SEC-002`  
**Severity:** 🔴 Critical  
**Estimated Time:** 6 hours  

**Problem:**
- No rate limiting on login, forms, or API endpoints
- Vulnerable to brute force attacks and DoS
- No spam protection for form submissions

**Files to Create/Modify:**
- `src/application/Services/RateLimiter.php` (new)
- `src/application/Middleware/RateLimitMiddleware.php` (new)
- `src/interfaces/HTTP/Kernel.php`
- `src/interfaces/HTTP/Actions/Auth/LoginAction.php`

**Implementation:**
```php
// 1. Create RateLimiter service with sliding window
class RateLimiter {
    public function isAllowed(string $key, int $maxAttempts, int $windowSeconds): bool {
        // Use SQLite/cache to track attempts
    }
    
    public function getRemainingAttempts(string $key): int {
        // Return remaining attempts
    }
}

// 2. Apply to sensitive routes
// Login: 5 attempts per minute
// Forms: 10 submissions per hour
// API: 100 requests per minute
```

**Acceptance Criteria:**
- [ ] Login limited to 5 attempts/minute
- [ ] Form submissions limited to 10/hour
- [ ] API endpoints limited to 100/minute
- [ ] Returns 429 Too Many Requests when exceeded
- [ ] Shows retry-after header
- [ ] IP-based + user-based limiting

---

### Task 3: Add Security Headers
**ID:** `SEC-003`  
**Severity:** 🟠 High  
**Estimated Time:** 2 hours  

**Problem:**
- Missing Content-Security-Policy
- Missing X-Frame-Options (clickjacking risk)
- Missing HSTS (downgrade attacks possible)
- Missing X-Content-Type-Options

**Files to Modify:**
- `src/interfaces/HTTP/Kernel.php`
- `src/interfaces/HTTP/Middleware/SecurityHeadersMiddleware.php` (new)

**Implementation:**
```php
// Add to Kernel::handle() or create middleware
$response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
$response->headers->set('X-Frame-Options', 'DENY');
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
$response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
```

**Acceptance Criteria:**
- [ ] All security headers present on every response
- [ ] CSP configured for TailwindCSS + Alpine.js
- [ ] HSTS enabled for production
- [ ] Test with securityheaders.com (aim for A+)

---

### Task 4: Harden Session Security
**ID:** `SEC-004`  
**Severity:** 🟠 High  
**Estimated Time:** 3 hours  

**Problem:**
- No secure cookie flags enforced
- Session ID not regenerated after login
- No session fixation protection

**Files to Modify:**
- `src/application/Services/SessionManager.php`
- `src/interfaces/HTTP/Actions/Auth/LoginAction.php`
- `config/session.php` (new)

**Implementation:**
```php
// 1. Configure secure session settings
ini_set('session.cookie_secure', '1');           // HTTPS only
ini_set('session.cookie_httponly', '1');         // No JS access
ini_set('session.cookie_samesite', 'Strict');    // CSRF protection
ini_set('session.use_strict_mode', '1');         // Prevent fixation
ini_set('session.gc_maxlifetime', '1800');       // 30 min timeout

// 2. Regenerate session ID after login
session_regenerate_id(true);

// 3. Set session timeout
$_SESSION['last_activity'] = time();
```

**Acceptance Criteria:**
- [ ] Secure cookie flags set
- [ ] Session ID regenerated on login/logout
- [ ] Session timeout implemented (30 min idle)
- [ ] Session fixation protection enabled
- [ ] SameSite=Strict cookie

---

## 🟠 HIGH Priority (Fix Within 1 Week)

### Task 5: Form Input Validation & Sanitization
**ID:** `SEC-005`  
**Severity:** 🟠 High  
**Estimated Time:** 6 hours  

**Problem:**
- Form submissions accept raw unvalidated data
- No field type validation against form schema
- XSS risk in form data storage/display

**Files to Modify:**
- `src/application/Services/FormValidationService.php` (new)
- `src/interfaces/HTTP/Actions/Frontend/DisplayFormAction.php`
- `domain/ValueObjects/Email.php` (extend)
- `domain/ValueObjects/PhoneNumber.php` (new)

**Implementation:**
```php
// 1. Create validation service
class FormValidationService {
    public function validate(array $data, array $schema): ValidationResult {
        foreach ($schema['fields'] as $field) {
            // Validate type, length, format
            // Sanitize HTML if allowed
        }
    }
}

// 2. Apply to form submissions
$validation = $this->validator->validate($formData, $form->schema());
if ($validation->fails()) {
    return $this->redirectBack()->with('errors', $validation->errors());
}
```

**Acceptance Criteria:**
- [ ] All form fields validated against schema
- [ ] Email fields validated with Email VO
- [ ] Max length enforced on all text fields
- [ ] HTML sanitized with HTMLPurifier (if allowed)
- [ ] File uploads validated (MIME, size)

---

### Task 6: Add Request Size Limits
**ID:** `SEC-006`  
**Severity:** 🟡 Medium  
**Estimated Time:** 2 hours  

**Problem:**
- No POST body size validation
- Potential DoS via large payloads
- File upload size not properly limited

**Files to Modify:**
- `src/interfaces/HTTP/Kernel.php`
- `.htaccess` or nginx config
- `php.ini` recommendations

**Implementation:**
```php
// 1. Check content length in Kernel
$contentLength = (int) $request->headers->get('Content-Length');
if ($contentLength > 10 * 1024 * 1024) { // 10MB
    throw new HttpException(413, 'Payload too large');
}

// 2. Configure in .htaccess
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

**Acceptance Criteria:**
- [ ] POST body limited to 10MB (configurable)
- [ ] File uploads limited to 10MB
- [ ] Returns 413 error when exceeded
- [ ] Document limits in README

---

### Task 7: Improve Error Messages
**ID:** `SEC-007`  
**Severity:** 🟡 Medium  
**Estimated Time:** 3 hours  

**Problem:**
- Some error messages may leak sensitive information
- Stack traces shown in debug mode could reveal paths
- Database errors might expose schema

**Files to Modify:**
- `src/interfaces/HTTP/Kernel.php`
- `src/application/Exceptions/Handler.php` (new)
- All catch blocks

**Implementation:**
```php
// 1. Create exception handler
class ExceptionHandler {
    public function render(Throwable $e): Response {
        // Log full error internally
        error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        
        // Return generic message to user
        return new Response(
            $this->getGenericMessage($e),
            $this->getStatusCode($e)
        );
    }
    
    private function getGenericMessage(Throwable $e): string {
        // Map exception types to generic messages
        // Never expose internal details
    }
}
```

**Acceptance Criteria:**
- [ ] Generic error messages in production
- [ ] Full errors logged internally only
- [ ] No stack traces in responses
- [ ] No database schema info leaked
- [ ] Custom error pages (404, 403, 500)

---

## 📋 Task Summary

| ID | Task | Priority | Est. Time | Status |
|----|------|----------|-----------|--------|
| SEC-001 | CSRF Protection | 🔴 Critical | 4h | ⏳ Pending |
| SEC-002 | Rate Limiting | 🔴 Critical | 6h | ⏳ Pending |
| SEC-003 | Security Headers | 🟠 High | 2h | ⏳ Pending |
| SEC-004 | Session Security | 🟠 High | 3h | ⏳ Pending |
| SEC-005 | Form Validation | 🟠 High | 6h | ⏳ Pending |
| SEC-006 | Request Size Limits | 🟡 Medium | 2h | ⏳ Pending |
| SEC-007 | Error Messages | 🟡 Medium | 3h | ⏳ Pending |

**Total Estimated Time:** 26 hours (~3-4 working days)

---

## 🎯 Implementation Order

1. **Week 1 (Critical):**
   - Day 1: SEC-001 (CSRF) + SEC-003 (Headers)
   - Day 2: SEC-002 (Rate Limiting)
   - Day 3: SEC-004 (Session Security)

2. **Week 2 (High/Medium):**
   - Day 4-5: SEC-005 (Form Validation)
   - Day 6: SEC-006 (Size Limits) + SEC-007 (Error Messages)

---

## 📊 Post-Implementation Goals

After completing all tasks:
- **Security Score:** Target 9/10
- **OWASP Top 10:** All covered
- **Production Ready:** ✅ Yes
- **Security Audit:** Pass

---

## 🔗 References

- OWASP Top 10 2021: https://owasp.org/www-project-top-ten/
- PHP Security Best Practices: https://www.php.net/manual/en/security.php
- Content Security Policy: https://content-security-policy.com/
- Security Headers: https://securityheaders.com/

---

*Generated by Security Checklist Skill on 2026-02-28*
