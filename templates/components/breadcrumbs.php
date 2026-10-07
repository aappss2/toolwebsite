<?php
// Breadcrumbs component - expects $breadcrumbs array of ['name'=>, 'url'=>]
if (empty($breadcrumbs)) return;
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <?php foreach ($breadcrumbs as $i => $crumb): ?>
    <?php if ($i > 0): ?><span>›</span><?php endif; ?>
    <?php if (!empty($crumb['url']) && $i < count($breadcrumbs)-1): ?>
      <a href="<?= e($crumb['url']) ?>"><?= e($crumb['name']) ?></a>
    <?php else: ?>
      <span><?= e($crumb['name']) ?></span>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>
