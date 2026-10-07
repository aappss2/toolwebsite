<?php
// Shared header - expects $pageTitle, $pageDescription, $canonicalUrl, $user
$user = $user ?? Auth::user();
$usage = UsageService::getUsageStatus($user);
$csrfToken = Csrf::getToken();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <title><?= e($pageTitle ?? 'PromptTai Tools - Powerful Online Tools') ?></title>
  <meta name="description" content="<?= e($pageDescription ?? 'Powerful Online Tools. One Simple Platform. Free PDF, Image, Calculator, AI tools.') ?>">
  <link rel="canonical" href="<?= e($canonicalUrl ?? APP_DOMAIN . '/') ?>">
  <meta name="csrf-token" content="<?= e($csrfToken) ?>">
  
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="/assets/css/app.css">
  
  <!-- SEO -->
  <?php if (!empty($ogTags)) echo $ogTags; ?>
  <?php if (!empty($breadcrumbSchema)) echo $breadcrumbSchema; ?>
  <?php if (!empty($toolSchema)) echo $toolSchema; ?>
  <?php if (!empty($faqSchema)) echo $faqSchema; ?>
  
  <!-- AdSense -->
  <?= AdService::getAdScripts() ?>
  
  <!-- Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(SITE_GTAG_ID) ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= e(SITE_GTAG_ID) ?>');
  </script>
</head>
<body>
  <header class="header">
    <div class="header-inner">
      <a href="/" class="brand">
        <div class="brand-logo">P</div>
        PromptTai
      </a>
      
      <div class="search-wrap">
        <span class="search-icon">🔍</span>
        <input id="globalSearch" class="search-input" type="search" placeholder="Search tools..." aria-label="Search tools">
        <div id="searchResults" class="search-results"></div>
      </div>
      
      <nav class="nav" aria-label="Primary">
        <a href="/" class="<?= ($_SERVER['REQUEST_URI'] ?? '/') === '/' ? 'active' : '' ?>">Home</a>
        <a href="/category/pdf-tools/">PDF</a>
        <a href="/category/image-tools/">Image</a>
        <a href="/category/calculators/">Calculators</a>
        <a href="/pricing">Pricing</a>
        <?php if ($user): ?>
          <a href="/dashboard">Dashboard</a>
          <a href="/account">Account</a>
          <?php if (Auth::isAdmin()): ?>
            <a href="/admin">Admin</a>
          <?php endif; ?>
          <span id="usageIndicator" class="usage-indicator <?= $usage['has_subscription'] ? 'pro' : ($usage['free_uses_remaining'] <=2 ? 'low' : '') ?>">
            <?php if ($usage['has_subscription']): ?>
              ⭐ Pro Active
            <?php else: ?>
              Free: <?= $usage['free_uses_remaining'] ?>/<?= $usage['free_uses_total'] ?>
            <?php endif; ?>
          </span>
          <a href="/logout" class="cta">Logout</a>
        <?php else: ?>
          <a href="/login">Login</a>
          <a href="/register" class="cta">Get Started</a>
        <?php endif; ?>
      </nav>
      
      <button id="menuToggle" aria-expanded="false" aria-label="Menu" style="display:none;background:none;border:none;font-size:1.5rem;cursor:pointer;">☰</button>
    </div>
  </header>
  <main class="wrap">
