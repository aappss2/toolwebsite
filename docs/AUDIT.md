# Full Source Audit — JDIRO Tools Website

Date: 2026-10-07
Old Domain: https://jdiro.com/
New Domain: https://tool.prompttai.com/
Branch: arena/16cbed84-toolwebsite
Commit Base: 4a06202cd855361ace07eebf753e28ef6fbcc009

## Repository Structure Overview

Root files only present in current checkout:
- .htaccess (493 bytes) — cPanel PHP handler + suspicious error document redirects to earncolour.xyz
- index.html (30861 bytes) — legacy homepage with glass/3D UI, Three.js, Tailwind CDN, duplicated AdSense, hardcoded analytics
- admin.php (2945 bytes) / dashboard.php (2617 bytes) — duplicate simple admin with hardcoded password, JSON file manipulation
- edit.php (1930 bytes) — edit tool metadata
- tools.json (34KB, 181 records) — canonical tool registry, but missing on latest main branch (deleted in 4a06202)
- aboutus.html, contact-us.html, privacy-policy.html, terms.html — static pages
- header.html, header.css, style.css, styles.css — fragmented CSS, duplicate frameworks
- sitemap.xml (24939 bytes) — manually maintained, includes URLs with spaces, inconsistent capitalization, old paths
- error_log (320 bytes) — contains PHP fatal error referencing .755931854218511.php — possible malware/backdoor inclusion attempt

No subdirectories for actual tools present in checkout. All tool implementations referenced in tools.json are missing from filesystem — they existed on production server but were not committed to git.

## File Classification

| File | Classification | Notes |
|------|---------------|-------|
| index.html | Core website | Legacy homepage, glass UI, loads Three.js on every page |
| aboutus.html, contact-us.html, privacy-policy.html, terms.html | Core website | Static content, inconsistent header/footer |
| admin.php, dashboard.php, edit.php | Admin functionality + Authentication functionality | Hardcoded passwords, no CSRF, no prepared statements (JSON only), duplicate |
| tools.json | Database (legacy) | 181 tools, fragile JSON-based admin, no validation |
| sitemap.xml | Core website / SEO | Manual, outdated, contains encoded spaces %20, inconsistent |
| .htaccess | Core website / Security concern | Redirects 500 errors to https://earncolour.xyz/ — external/obsolete, must remove |
| header.html, header.css, style.css, styles.css | Shared asset | Duplicate CSS, obsolete, unused frameworks |
| error_log | Backup/archive/junk + Security | Contains reference to missing include file — investigate for backdoor |
| README.md | Core website | Minimal |

### Missing Categories (Expected but not in repo)
- public_html/ structure
- actual tool folders: PDF/, IMAGE/, Utility Tools/, Educational Tools/, etc.
- /AI APPS/
- software.jdiro.com — referenced in audit checklist, not found in repo (external/obsolete)
- ai.jdiro.com — referenced as external subdomain in tools.json (12 tools)
- ai.earncolour.xyz — not found directly, but earncolour.xyz referenced in .htaccess
- nested ZIP files — none in repo
- Python package folders — none
- logs — only error_log present
- temporary files — none
- duplicated applications — admin.php vs dashboard.php duplicate
- old admin systems — yes, admin.php/edit.php
- test files — none
- old databases — tools.json acts as DB
- .well-known — not present
- unused scripts — Three.js loaded globally though only needed for hero
- obsolete CSS — style.css + styles.css + header.css + Tailwind CDN duplicate
- old analytics — Ahrefs, AdSense duplicated twice, gtag G-DPSML6XTSC
- hard-coded credentials — admin.php password durbyash, dashboard.php yourpassword
- hard-coded API keys — AdSense ca-pub-8977523182593956, Ahrefs key, gtag ID
- old payment code — none
- old authentication code — simple session + hardcoded password

## Security Observations (Summary)

- Hardcoded admin passwords in plaintext PHP
- No CSRF protection on add/delete/edit
- No input validation beyond required attribute
- No rate limiting
- Session fixation possible (no regeneration)
- .htaccess redirects errors to external domain earncolour.xyz — potential phishing / data leak
- error_log shows failed include of .755931854218511.php — possible obfuscated backdoor attempt, file not in repo, should check production server for hidden files
- Duplicate AdSense scripts (2x) — performance + policy risk
- Tailwind CDN loaded via cdn.jsdelivr.net — no integrity check
- Three.js from unpkg without SRI
- No Content-Security-Policy, X-Frame-Options, etc.
- No .env or config outside public root — everything in public

## Performance Observations

- Tailwind browser CDN on every admin page — heavy
- Three.js loaded on homepage even for non-3D pages
- Glass UI with backdrop-filter expensive on mobile
- No asset minification
- No cache headers
- No lazy-loading
- Duplicate library loads

## SEO Observations

- Canonical points to old domain https://jdiro.com/
- Sitemap manual, includes URLs with spaces (Utility Tools/Age-Calculator.html) which require encoding
- No structured data for tools
- Inconsistent URL capitalization (PDF/Mergepdf.html vs PDF/Splitpdf.html vs Pdftoimg.html)
- No robots.txt
- Title meta generic, not tool-specific

## Recommended Cleanup Actions

1. Remove .htaccess error redirects to earncolour.xyz
2. Remove hardcoded passwords, replace with proper auth
3. Move tools.json to MySQL
4. Archive legacy CSS into /legacy/
5. Remove duplicate AdSense loads, centralize ad component
6. Remove Three.js from global, load only where needed
7. Create unified header/footer/navbar templates
8. Normalize tool URLs to /tools/{slug}/
9. Create new architecture as per Phase 2 spec
10. Preserve tool logic where available, but since files missing, reconstruct canonical tool stubs from titles and implement generic calculators where logic can be inferred

## Legacy Junk to Archive

- admin.php, dashboard.php, edit.php — move to /legacy/
- header.html, header.css, style.css, styles.css — archive
- old sitemap.xml — archive after generating new one
- error_log — archive and clear
- .htaccess old version — archive

## Counts

- Total files in repo: 14 (excluding .git)
- Total tools in JSON: 181
- Total categories in JSON: 18 distinct raw categories
- External tools (ai.jdiro.com): 12
- Tools with spaces in link: ~ 80+
- Tools with inconsistent capitalization: ~ 60+
- Tools missing from filesystem: 181 (all, since no subfolders)

## Conclusion

The repository is a minimal landing page + JSON registry, not the full production codebase. The full tool implementations are missing. Migration must reconstruct a modern SaaS platform while preserving the inventory and intent of the 181 tools. The new architecture should not blindly copy legacy junk but build a clean, secure, scalable foundation.

