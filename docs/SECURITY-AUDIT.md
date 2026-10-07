# Security Audit — JDIRO Tools Website

Date: 2026-10-07
Auditor: Automated + Manual Review

## Summary

Legacy codebase has multiple high-severity issues typical of quickly assembled tool aggregator sites. No evidence of modern security practices. No prepared statements (though JSON storage avoids SQLi), no CSRF, no rate limiting, hardcoded credentials, external error redirects.

## Findings

### Critical

1. **Hardcoded Admin Passwords**
   - File: admin.php line 6: `if ($_POST['password'] ?? '' === 'durbyash')`
   - File: dashboard.php line 6: `if ($_POST['password'] ?? '' === 'yourpassword')`
   - Impact: trivial admin bypass if password known, no brute force protection
   - Redacted: [SECRET_REDACTED] and [SECRET_REDACTED] — must be rotated and removed
   - Fix: Replace with proper user table, password_hash(), rate limiting, CSRF

2. **ErrorDocument Redirect to External Domain**
   - File: .htaccess
   - ```
     ErrorDocument 500 https://earncolour.xyz/
     ErrorDocument 502 https://earncolour.xyz/
     ErrorDocument 503 https://earncolour.xyz/
     ErrorDocument 504 https://earncolour.xyz/
     ```
   - Impact: Leaks error context to external domain, potential phishing, SEO poison, may expose stack traces to third party
   - Fix: Remove, use local error pages, log internally

3. **Potential Backdoor Reference**
   - File: error_log: `Failed opening required '/home/jdirocom/public_html/.755931854218511.php'`
   - File starting with dot and numeric — common obfuscation for webshells
   - Impact: Production server may have hidden webshell, must audit all dotfiles, check for eval, base64_decode
   - Fix: List all hidden files on production, remove unknown, scan with mal scanner

### High

4. **No CSRF Protection**
   - admin.php, edit.php, dashboard.php all perform state-changing operations (add/delete/edit) via GET/POST without token
   - Impact: CSRF can add malicious tools linking to phishing
   - Fix: Implement CSRF tokens for all state-changing endpoints

5. **No Input Validation / Output Escaping in Admin**
   - While htmlspecialchars used on output, link field not validated — could inject javascript: URLs or external phishing
   - File upload not present in legacy, but tools.json link could be set to malicious external URL
   - Fix: Validate URL format, allowlist schemes, sanitize

6. **Session Fixation**
   - session_start() without regeneration on login
   - Impact: Session fixation attack
   - Fix: session_regenerate_id(true) on login

7. **No Rate Limiting**
   - Login attempts unlimited, tool endpoints unlimited
   - Impact: Brute force, abuse
   - Fix: Implement rate limiting middleware

8. **Exposed Credentials in Frontend**
   - AdSense client ca-pub-[SECRET_REDACTED] hardcoded — not secret but should be centralized
   - Ahrefs analytics key [SECRET_REDACTED] hardcoded
   - gtag ID [SECRET_REDACTED]
   - Impact: Not critical but should be in config
   - Fix: Centralize in site_settings table

### Medium

9. **Duplicate AdSense Scripts**
   - index.html loads AdSense twice — could trigger policy violation
   - Fix: Single centralized ad component

10. **No Security Headers**
    - Missing: X-Content-Type-Options, X-Frame-Options, Referrer-Policy, CSP, HSTS
    - Fix: Add via .htaccess or PHP header

11. **Tailwind CDN without SRI**
    - `<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">`
    - Impact: If CDN compromised, XSS
    - Fix: Self-host or add integrity, use local CSS

12. **Three.js from unpkg without SRI**
    - Similar risk

13. **No File Upload Validation (Future Risk)**
    - Many tools require file upload (PDF, images) but legacy implementation likely client-side only. For new platform, must validate MIME, extension, file signature, size, randomized names, store outside public root, clean temp files.

14. **No Prepared Statements (Future)**
    - Legacy uses JSON, but new MySQL must use PDO prepared statements everywhere

15. **No Secrets Management**
    - Database credentials, payment secrets must not be in public files
    - Must use config outside public root or .env not web-accessible

### Low / Info

16. **Verbose Error Logging**
    - error_log in public root — should be outside or protected
    - Fix: Move to /storage/logs, deny web access

17. **Insecure Direct Object Reference**
    - edit.php?index=XX — no authz check beyond simple admin session, sequential IDs
    - Fix: Use UUIDs or proper authz

18. **Missing robots.txt**
    - No robots.txt — should have one for new domain

## Hardcoded Secrets Found (Redacted)

- Admin password 1: [SECRET_REDACTED] in admin.php
- Admin password 2: [SECRET_REDACTED] in dashboard.php
- AdSense: [SECRET_REDACTED]
- Ahrefs: [SECRET_REDACTED]
- gtag: [SECRET_REDACTED]
- No API keys for Razorpay/Stripe found — must be added securely for new platform

## Recommendations for New Platform

1. **Authentication**
   - password_hash() with PASSWORD_DEFAULT
   - password_verify()
   - Secure sessions, regeneration, timeout 2h, HttpOnly, Secure, SameSite
   - CSRF tokens
   - Rate limiting: 5 login attempts per 15 min per IP + email
   - Password reset with expiring tokens (1h), single use

2. **Authorization**
   - Roles: user, admin
   - Permissions table
   - Admin routes protected by middleware

3. **Input Validation**
   - All inputs validated server-side
   - Use filter_var, regex, whitelisting
   - File uploads: MIME check, extension check, file signature (finfo), size limit (10MB default), randomized filename, no execution

4. **SQL Injection**
   - PDO with prepared statements, no string concatenation

5. **XSS**
   - Escape output with htmlspecialchars ENT_QUOTES
   - CSP header

6. **CSRF**
   - Token per session/form, verify on POST

7. **Secrets**
   - config.php outside public root
   - .env not in public, deny via .htaccess
   - Never expose secrets in HTML/JS

8. **Payment**
   - Never trust client-side payment success
   - Verify server-side via Razorpay signature
   - Webhook signature verification
   - Idempotency keys for payment_events
   - Store only gateway IDs, not secrets in DB logs

9. **Headers**
   - X-Content-Type-Options: nosniff
   - X-Frame-Options: SAMEORIGIN (or CSP frame-ancestors)
   - Referrer-Policy: strict-origin-when-cross-origin
   - Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://checkout.razorpay.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; etc.
   - Strict-Transport-Security after HTTPS confirmed

10. **File System**
    - Deny access to /app, /config, /storage, /database via .htaccess
    - Disable directory listing
    - Uploads outside public or with .htaccess deny php execution

11. **Logging**
    - Admin logs, billing logs, usage logs
    - No sensitive data in logs
    - Rotate logs

12. **Rate Limiting**
    - Login, register, password reset, payment, API, tool execution

13. **Dependency Scanning**
    - No composer dependencies yet, but when added, audit

## Immediate Actions Taken for New Platform

- New .htaccess will deny access to sensitive files
- New config will be outside public root
- All legacy hardcoded passwords removed, not carried forward
- External error redirects removed
- New auth system implements all recommended controls

## Credential Rotation Required

- [ ] Rotate AdSense if needed (not secret but verify ownership)
- [ ] Rotate Ahrefs key
- [ ] Remove earncolour.xyz references and investigate production server for hidden files
- [ ] Audit production server for .755931854218511.php and similar dotfiles
- [ ] Change all cPanel passwords
- [ ] Generate new Razorpay keys for new domain, store securely

