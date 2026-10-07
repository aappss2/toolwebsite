<?php
http_response_code(404);
$pageTitle = "Page Not Found - " . APP_NAME;
include __DIR__ . '/header.php';
?>
<div class="empty-state">
  <div class="empty-state-icon">🔍</div>
  <h1>Page Not Found</h1>
  <p>The page you're looking for doesn't exist or has been moved.</p>
  <div style="margin-top:20px;">
    <a href="/" class="btn btn-primary">Go Home</a>
    <a href="/category/all/" class="btn btn-secondary">Browse Tools</a>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
