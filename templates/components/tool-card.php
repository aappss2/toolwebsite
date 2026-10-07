<?php
// Tool card component - expects $tool
$tool = $tool ?? [];
?>
<a href="/tools/<?= e($tool['slug']) ?>/" class="tool-card">
  <div class="tool-card-icon"><?= e($tool['icon'] ?? substr($tool['name'] ?? 'T', 0, 1)) ?></div>
  <h3><?= e($tool['name'] ?? 'Tool') ?></h3>
  <p><?= e(substr($tool['description'] ?? 'Free online tool', 0, 100)) ?></p>
  <div class="tool-card-meta">
    <?php if (!empty($tool['category_name'])): ?>
      <span class="badge"><?= e($tool['category_name']) ?></span>
    <?php endif; ?>
    <?php if (!empty($tool['featured'])): ?>
      <span class="badge badge-primary">Featured</span>
    <?php endif; ?>
  </div>
</a>
