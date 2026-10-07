# PromptTai Tools — https://tool.prompttai.com/

Modern SaaS tools platform migrated from legacy JDIRO (https://jdiro.com/).

## Overview
- 181 tools migrated to unified UI
- PHP 8.2 + MySQL + Apache/cPanel compatible
- Subscription + 10 free uses model
- Razorpay integration with server-side verification
- Secure, SEO-friendly, mobile-first

## Structure
- `/app` — Core, Auth, Billing, Usage, Services
- `/public` — Document root (set cPanel to this folder), front controller index.php
- `/templates` — Unified header/footer/tool layout
- `/tools` — 181 tool implementations (each with index.php + metadata.json)
- `/api` — Auth, Tools, Usage, Billing endpoints
- `/database` — schema.sql + migrations
- `/storage` — logs, cache, temp uploads (outside public where possible)
- `/docs` — Audit, inventory, migration map, security audit, architecture, logs
- `/legacy` — Archived old files

## Quick Start (Local Dev)

1. Copy `.env.example` to `.env` and configure
2. Ensure `storage/database.sqlite` exists (fallback) or configure MySQL
3. For SQLite dev: python seeding script already created 181 tools
4. Set document root to `/public` or use root `index.php` that includes public/index.php
5. Run PHP server: `php -S 0.0.0.0:8000 -t public/`

For cPanel:
- Upload all files, set public_html to point to `/public` or copy public contents to public_html and adjust paths
- Import `database/schema.sql` into MySQL
- Configure `.env` outside public root
- Ensure `storage/` is writable

## Admin
- Default admin: admin@prompttai.com / password (hash in DB, change immediately)
- Admin dashboard: /admin
- Users: /admin/users, Tools: /admin/tools, etc (extend as needed)

## Free Uses
- Configurable via `site_settings` table `free_uses_default` = 10
- Enforced server-side in UsageService

## Payments
- Razorpay: create order via /api/billing/create-order.php, verify via /api/billing/verify.php, webhook via /api/billing/webhook.php
- Never trust client-side success — server verification required
- Stripe stub ready for future

## SEO
- Dynamic sitemap at /sitemap.xml
- Robots at /robots.txt
- Canonical URLs on new domain
- Migration map in docs/MIGRATION-MAP.csv for 301 redirects

## Security
- See docs/SECURITY-AUDIT.md
- Hardcoded secrets removed, use .env
- CSRF, rate limiting, prepared statements, secure sessions

## Migration Status
- 181 tools discovered
- 181 migrated (including 12 external AI tools wrapped)
- See docs/MIGRATION-LOG.md and FINAL-AUDIT.md

## Legacy
- Old files archived in /legacy/
- Old .htaccess with earncolour.xyz redirects removed

## License
Private — PromptTai
