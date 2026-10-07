# Architecture — New Platform https://tool.prompttai.com/

## Goals
- Unified SaaS feel, not collection of HTML pages
- PHP 8.2 + MySQL + Apache/cPanel compatible
- MVC-like separation without heavy framework
- Secure, fast, SEO-friendly, mobile-first
- Subscription + 10 free uses model
- Centralized usage gating
- Admin configurable pricing, categories, tools

## Recommended Structure

```
/app
  /config
    config.php          — loads env, DB, constants
    constants.php       — FREE_USES_DEFAULT etc
  /core
    Database.php        — PDO wrapper
    Router.php          — simple router
    Auth.php            — session, login, CSRF
    Csrf.php
    RateLimiter.php
    Validator.php
    Seo.php
    Security.php        — headers
  /auth
    UserService.php
    PasswordResetService.php
  /billing
    PaymentGatewayInterface.php
    RazorpayGateway.php
    StripeGateway.php (stub)
    SubscriptionService.php
    WebhookHandler.php
  /usage
    UsageService.php    — canUseTool, consumeUsage, etc
  /admin
    AdminService.php
  /services
    ToolService.php
    CategoryService.php
    SearchService.php
    AdService.php
    UploadService.php
  /helpers
    helpers.php
    slug.php
    url.php

/public
  index.php             — front controller
  .htaccess             — rewrite to index.php, security headers, deny sensitive
  robots.txt
  sitemap.xml           — dynamic via router
  manifest.webmanifest
  service-worker.js
  /assets
    /css
      app.css           — unified design system
    /js
      app.js
      search.js
      tool.js
    /images
  /uploads              — protected, no php execution

/templates
  header.php
  footer.php
  navbar.php
  tool-layout.php
  category-layout.php
  auth-layout.php
  account-layout.php
  components/
    ad.php
    usage-indicator.php
    upgrade-modal.php
    breadcrumbs.php
    tool-card.php
    category-card.php

/tools
  /tool-slug/
    index.php           — thin wrapper using template + logic
    tool.js
    tool.css
    metadata.json

/api
  /auth
    login.php
    register.php
    logout.php
  /tools
    execute.php         — usage gated execution
    search.php
  /usage
    status.php
  /billing
    create-order.php
    verify.php
    webhook.php
  /admin
    ...

/database
  schema.sql
  migrations/
    001_initial.sql
    002_seed_categories.sql
    003_seed_tools.sql

/storage
  /logs
  /cache
  /uploads_tmp          — temp files, outside public if possible

/docs
  AUDIT.md
  TOOL-INVENTORY.md
  MIGRATION-MAP.csv
  SECURITY-AUDIT.md
  ARCHITECTURE.md
  MIGRATION-LOG.md
  FINAL-AUDIT.md

/legacy                — archived old files
```

## Request Flow

1. Apache .htaccess rewrites all non-file requests to /public/index.php
2. index.php bootstraps /app/config/config.php
3. Router parses URL
   - / -> homepage
   - /tools/{slug}/ -> tool page
   - /category/{slug}/ -> category page
   - /pricing -> pricing
   - /login, /register, etc -> auth
   - /account/* -> account
   - /admin/* -> admin (role check)
   - /api/* -> API endpoints (JSON)
   - /sitemap.xml -> dynamic sitemap
   - /robots.txt -> static or dynamic
4. Controllers load services, check auth, usage, render templates

## Database Design

See /database/schema.sql — includes:
- users, user_sessions, password_resets, roles, permissions
- categories (with parent_id for hierarchy)
- tools, tool_aliases
- tool_usage, api_usage
- subscriptions, subscription_plans, payments, payment_events, billing_logs
- admin_logs, site_settings, contact_messages, migration_map

Key indexes: users.email unique, tools.slug unique, tool_aliases.alias unique, tool_usage user_id+tool_id+created_at

## Authentication

- Email/password registration
- password_hash() PASSWORD_DEFAULT, password_verify()
- Secure sessions: httponly, secure, samesite Lax, regeneration on login, timeout 2h
- CSRF token per form, stored in session
- Rate limiting: login 5/15min, register 3/15min, password reset 3/15min, stored in file or DB
- Password reset: random token 64 chars, hashed in DB, expires 1h, single use
- Roles: user, admin — middleware checks

## Free Usage Model

- Config: FREE_USES_DEFAULT = 10 (admin configurable via site_settings)
- users.free_uses_remaining, total_free_uses, free_uses_used
- On registration: set remaining = FREE_USES_DEFAULT
- UsageService:
  - canUseTool(user, tool): if hasActiveSubscription => true; else if free_uses_remaining >0 => true; else false
  - consumeUsage(user, tool, success): if not subscribed and success, decrement remaining, increment used, insert tool_usage row
  - hasActiveSubscription(user): check subscriptions status active and current_period_end > now
  - getUsageStatus(user): returns remaining, used, subscription status
- Enforcement server-side in /api/tools/execute.php and in tool pages before processing
- Anonymous: optional small trial counter via cookie/localStorage but primary is auth — we will allow 2 anonymous uses per IP per day, but encourage login

## Subscription System

- Interface PaymentGatewayInterface with methods: createCustomer, createSubscription, createOrder, verifyPayment, verifyWebhookSignature, cancelSubscription
- RazorpayGateway implements
- StripeGateway stub for future
- Plans table: id, name, slug, description, amount, currency, billing_interval (month/year), interval_count, status, features JSON
- Subscriptions: user_id, plan_id, gateway, gateway_subscription_id, status, started_at, current_period_start, current_period_end, cancelled_at
- Flow:
  1. User clicks Upgrade -> /account/billing -> choose plan
  2. Frontend calls /api/billing/create-order.php -> creates Razorpay order, returns order_id
  3. Razorpay checkout
  4. Frontend calls /api/billing/verify.php with razorpay_payment_id, order_id, signature
  5. Server verifies signature using secret, verifies payment status via API, activates subscription, logs payment, billing_logs
  6. Webhook: /api/billing/webhook.php verifies signature, handles subscription renewal, payment failure, cancellation, idempotency via payment_events table
- Never activate subscription solely because browser says success
- Store keys in config outside public root

## Usage Gating

- All metered tools must call UsageService
- Centralized, not duplicated per tool
- Before execution: check canUseTool
- After successful execution: consumeUsage
- Failed execution should not consume where technically possible — tool JS should report success/failure to server

## Unified Tool UI

- Global design system in /public/assets/css/app.css
- Variables: --primary, --secondary, --bg, --card, --radius, --shadow, --font
- Components: header, navbar, footer, breadcrumbs, category badge, tool title, description, workspace, actions, usage indicator, upgrade CTA, related tools, FAQ, SEO content
- Professional SaaS: clean, fast, modern, responsive, mobile-first, accessible
- One design system: modern cards, subtle shadows, rounded corners, consistent spacing, typography, responsive, light/dark mode, keyboard accessible, clear CTA, consistent form controls, buttons, progress indicators, drag-drop, toast, modal, loading, empty, error, success states
- Outer experience standardized, inner workspace preserved per tool type

## Tool Template

Structure per /templates/tool-layout.php:
- Breadcrumb
- Category badge
- H1
- Short description
- Usage indicator (free uses remaining or Pro active)
- Tool workspace (form inputs, file upload, canvas, etc)
- Action buttons (Calculate, Convert, Download, etc)
- Result area
- Ad slots (top, in-content, bottom) via AdService
- Related tools
- How to use
- FAQ
- SEO content
- Privacy notice where appropriate

URL: /tools/{slug}/ — never /Utility Tools/Age-Calculator.html

## Categories

Normalized hierarchy:
- AI Tools
- PDF Tools
- Image Tools
- Audio & Video Tools
- Calculators
  - General
  - Financial
  - Health & Fitness
  - Educational
  - Sports
- Financial Calculators (sub of Calculators)
- Educational Tools
- Health & Fitness
- Sports
  - Baseball
  - Basketball
  - Cricket
- Developer Tools / Webmaster Tools
- Text & Document Tools
- Utility Tools
- Compression Tools
- Presentation Tools
- Fun & Games

Implementation: categories table with parent_id, slug, name, description, icon, status, sort_order

## Category Pages

- /category/{slug}/
- Title, SEO description, tool count, search, filter, sort, tool cards, pagination, featured, related categories

## Homepage

Sections:
1. Sticky navigation with logo, search, account, usage indicator
2. Hero: "Powerful Online Tools. One Simple Platform."
3. Popular tools (by usage)
4. Categories grid
5. Featured AI tools
6. PDF tools
7. Image tools
8. Calculators
9. Why use platform (benefits)
10. Pricing teaser
11. FAQ
12. Footer

Keep subtle visual character from old glass/3D but prioritize performance — no Three.js on every page, only hero optional with low-poly and reduced motion support.

## Tool Search

- Global instant search
- Search across tool name, slug, description, category, keywords
- Features: instant suggestions, keyboard nav, category labels, popular searches, mobile-friendly
- Central tool index via /api/tools/search.php querying tools table with LIKE or fulltext
- JS: debounce, fetch, render

## Admin Dashboard

Pages:
- /admin — overview analytics
- /admin/users — search, view, suspend, activate, reset password, inspect subscription, usage, grant credits
- /admin/tools — CRUD, enable/disable, feature, change category, slug, metadata, SEO, free/paid, usage cost
- /admin/categories — CRUD, reorder, enable/disable
- /admin/plans — CRUD, activate/deactivate
- /admin/subscriptions — list, view, cancel
- /admin/payments — list, view, logs
- /admin/usage — analytics, popular tools
- /admin/settings — site settings, FREE_USES_DEFAULT, ad slots, SEO
- /admin/analytics — total users, active, free usage, paid, subscriptions, revenue, tool usage, popular, category usage, failed payments
- /admin/migration — view migration map, status
- /admin/logs — admin_logs, billing_logs

All admin protected, CSRF, audit logged

## Migration Engine

For each legacy tool:
1. Read original file (if exists) — here missing, so infer from title
2. Identify logic
3. Identify deps
4. Extract JS logic (if available)
5. Extract backend logic
6. Identify input/output
7. Preserve calculations/algorithms (re-implement generic calculators)
8. Preserve SEO content
9. Preserve FAQ
10. Create canonical slug
11. Assign category
12. Create metadata
13. Put inside standard layout
14. Add usage protection
15. Add related tools
16. Add canonical URL
17. Add breadcrumb
18. Add responsive styling
19. Add loading/error/success
20. Test
21. Mark complete in MIGRATION-LOG.md

## Security Cleanup

- Remove hardcoded secrets, use config outside public root
- .env protection via .htaccess deny
- Security headers
- Prepared statements everywhere
- Escape output
- Validate uploads: MIME, extension, signature, size, randomized name, prevent executable, store outside public root, auto-clean
- Protect admin endpoints
- CSRF on state-changing
- Rate limiting

## SEO

- Every tool page: unique title, meta description, canonical, OG, Twitter, structured data (SoftwareApplication, BreadcrumbList, FAQPage), robots, internal links, related tools
- /robots.txt
- /sitemap.xml dynamic from active canonical pages, exclude admin, login, dashboard, private, test, draft, duplicate
- Use clean canonical URLs on NEW_DOMAIN

## Domain Migration

- Create MIGRATION-MAP.csv old_url -> new_url 301
- Example: https://jdiro.com/PDF/Mergepdf.html -> https://tool.prompttai.com/tools/pdf-merger/
- Do not redirect all to homepage — direct page-to-page
- Update internal links, canonical, OG, sitemaps, robots, structured data, navigation, assets
- Keep redirects long term via .htaccess or router

## Payment and Business Model

- /pricing — FREE 10 uses, PRO MONTHLY, PRO YEARLY — editable from admin
- Show features, limits, billing interval, currency, upgrade button, current plan, subscription status
- /account/billing — current plan, state, renewal, history, cancel, upgrade, downgrade

## Tool Usage UX

- Header/account area: "Free uses remaining: 7/10" or "Pro Plan — Active"
- Tool page: usage indicator
- When 1-2 remaining: non-annoying upgrade prompt
- When quota exhausted: upgrade modal/page with clear CTA, not deceptive

## Performance

- No Tailwind browser CDN in production — use compiled app.css
- Remove duplicate lib loads, duplicate AdSense
- Minimize JS, lazy-load images, cache headers, compressed assets
- Avoid Three.js on every tool page, only hero if needed, prefer CSS

## Adsense / Monetization

- Centralized ad component: ad_top(), ad_in_content(), ad_bottom()
- Admin config for ad slots
- Do not place ads where interfere with tool controls
- Paid users optionally ad-light or ad-free

## PWA / Mobile

- Excellent on Android, iPhone, tablet, desktop
- Mobile nav, touch targets, responsive forms, tables, file upload, download, dark mode, installable PWA where practical
- manifest.webmanifest, service-worker.js optional

## Analytics

Events: tool_view, tool_started, tool_completed, tool_failed, free_usage_consumed, upgrade_clicked, checkout_started, payment_success, subscription_active, subscription_cancelled
Do not send sensitive input/output

## Legacy Code Policy

- Move unused/obsolete to /legacy/ or archive outside public root
- After verified, obsolete may be removed
- Never deploy backup ZIPs, logs, credentials, notes, dumps, venv, archives

## Testing

Checklist per tool: HTTP 200, title, canonical, mobile, desktop, console, execution, error handling, download, upload, quota enforcement, subscription bypass, anon, login, logout
Security tests: SQLi, XSS, CSRF, auth bypass, authz bypass, upload abuse, path traversal, rate limiting, payment callback spoofing, webhook replay/idempotency

## Final Quality Check

- PHP syntax checks
- DB schema validation
- Broken-link scan
- Asset reference scan
- URL scan
- Secret scan
- Duplicate tool scan
- Duplicate metadata scan
- Missing canonical, meta description, title scan
- Console error scan
- HTTP status scan
- Sitemap validation
- Generate FINAL-AUDIT.md

