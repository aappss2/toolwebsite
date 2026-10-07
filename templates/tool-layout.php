<?php
// Tool layout - expects $tool, $relatedTools, $faqs, $user
$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => $tool['category_name'] ?? 'Tools', 'url' => '/category/' . ($tool['category_slug'] ?? 'all') . '/'],
    ['name' => $tool['name']]
];
$canonicalUrl = APP_DOMAIN . '/tools/' . $tool['slug'] . '/';
$pageTitle = Seo::generateTitle($tool['name'], $tool['category_name'] ?? '');
$pageDescription = Seo::generateDescription($tool['name'], $tool['category_name'] ?? '', $tool['meta_description'] ?? '');
$ogTags = Seo::ogTags($pageTitle, $pageDescription, $canonicalUrl, $tool['image'] ?? '');
$breadcrumbSchema = Seo::breadcrumbSchema(array_map(fn($b) => ['name'=>$b['name'], 'url'=> isset($b['url']) ? APP_DOMAIN . $b['url'] : $canonicalUrl], $breadcrumbs));
$toolSchema = Seo::softwareAppSchema($tool);
$faqSchema = !empty($faqs) ? Seo::faqSchema($faqs) : '';

include TEMPLATE_PATH . '/header.php';
?>

<?php include TEMPLATE_PATH . '/components/breadcrumbs.php'; ?>

<div class="tool-layout">
  <div class="tool-main">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
      <span class="badge badge-primary"><?= e($tool['category_name'] ?? 'Tool') ?></span>
      <?php if (!empty($tool['featured'])): ?><span class="badge">Featured</span><?php endif; ?>
    </div>
    <h1 style="margin:0 0 8px;"><?= e($tool['name']) ?></h1>
    <p style="color:var(--text-muted);margin:0 0 20px;"><?= e($tool['description'] ?? $pageDescription) ?></p>

    <?= AdService::adTop($user) ?>

    <!-- Tool Workspace -->
    <div id="toolWorkspace" class="tool-workspace">
      <!-- Tool-specific content will be injected here -->
      <?php if (!empty($toolContent)) echo $toolContent; ?>
    </div>

    <!-- Result Area -->
    <div id="toolResult" class="tool-result" style="margin-top:24px;display:none;">
      <h3>Result</h3>
      <div id="resultContent" style="background:var(--bg);padding:16px;border-radius:var(--radius);border:1px solid var(--border);"></div>
    </div>

    <?= AdService::adInContent($user) ?>

    <!-- How to use -->
    <div style="margin-top:32px;">
      <h3>How to use <?= e($tool['name']) ?></h3>
      <ol style="color:var(--text-muted);">
        <li>Enter your input in the tool workspace above</li>
        <li>Click Calculate/Convert/Process button</li>
        <li>View and download your result</li>
        <li>Free users: <?= FREE_USES_DEFAULT ?> free uses, Pro: unlimited</li>
      </ol>
    </div>

    <!-- FAQ -->
    <?php if (!empty($faqs)): ?>
    <div style="margin-top:32px;">
      <h3>FAQ</h3>
      <dl>
        <?php foreach ($faqs as $faq): ?>
          <dt style="font-weight:700;margin-top:12px;"><?= e($faq['q']) ?></dt>
          <dd style="margin:6px 0 12px;color:var(--text-muted);"><?= e($faq['a']) ?></dd>
        <?php endforeach; ?>
      </dl>
    </div>
    <?php endif; ?>

    <?= AdService::adBottom($user) ?>

    <!-- Related tools -->
    <?php if (!empty($relatedTools)): ?>
    <div style="margin-top:32px;">
      <h3>Related Tools</h3>
      <div class="tool-grid">
        <?php foreach ($relatedTools as $rt): ?>
          <?php $tool = $rt; include TEMPLATE_PATH . '/components/tool-card.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <div class="tool-sidebar">
    <?php $usage = UsageService::getUsageStatus($user); include TEMPLATE_PATH . '/components/usage-indicator.php'; ?>
    
    <div class="card">
      <h4 style="margin:0 0 12px;">Tool Info</h4>
      <p style="font-size:0.9rem;color:var(--text-muted);margin:0;">Category: <?= e($tool['category_name'] ?? 'General') ?></p>
      <p style="font-size:0.9rem;color:var(--text-muted);margin:8px 0 0;">Type: <?= e($tool['type'] ?? 'Utility') ?></p>
      <p style="font-size:0.9rem;color:var(--text-muted);margin:8px 0 0;">Views: <?= number_format($tool['view_count'] ?? 0) ?></p>
    </div>

    <div class="card">
      <h4 style="margin:0 0 12px;">Privacy</h4>
      <p style="font-size:0.85rem;color:var(--text-muted);margin:0;">Your files are processed securely and deleted after processing. We don't store your data.</p>
    </div>
  </div>
</div>

<?php include TEMPLATE_PATH . '/footer.php'; ?>
