<?php
// Usage indicator component
$user = $user ?? Auth::user();
$usage = $usage ?? UsageService::getUsageStatus($user);
?>
<div class="card" style="padding:16px;">
  <h4 style="margin:0 0 8px;">Usage</h4>
  <?php if ($usage['has_subscription']): ?>
    <div class="usage-indicator pro" style="justify-content:center;">⭐ Pro Plan — Active</div>
    <?php if (!empty($usage['subscription_expires_at'])): ?>
      <p style="font-size:0.85rem;color:var(--text-muted);margin:8px 0 0;">Renews: <?= e(date('M d, Y', strtotime($usage['subscription_expires_at']))) ?></p>
    <?php endif; ?>
  <?php else: ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
      <span>Free uses</span>
      <strong><?= $usage['free_uses_remaining'] ?>/<?= $usage['free_uses_total'] ?></strong>
    </div>
    <div style="background:var(--bg);border-radius:999px;height:8px;overflow:hidden;">
      <div style="height:100%;background:linear-gradient(90deg,var(--primary),var(--secondary));width:<?= ($usage['free_uses_total'] >0 ? (100 - ($usage['free_uses_remaining']/$usage['free_uses_total']*100)) : 0) ?>%"></div>
    </div>
    <?php if ($usage['free_uses_remaining'] <= 2 && $usage['free_uses_remaining'] >0): ?>
      <p style="font-size:0.85rem;color:#92400e;background:#fef3c7;padding:8px;border-radius:8px;margin-top:12px;">⚠️ Only <?= $usage['free_uses_remaining'] ?> free uses left. <a href="/pricing">Upgrade</a></p>
    <?php elseif ($usage['free_uses_remaining'] <=0): ?>
      <p style="font-size:0.85rem;color:#991b1b;background:#fee2e2;padding:8px;border-radius:8px;margin-top:12px;">You've used all free uses. <a href="/pricing">Upgrade to continue</a></p>
    <?php endif; ?>
  <?php endif; ?>
  <div style="margin-top:12px;display:flex;gap:8px;">
    <a href="/pricing" class="btn btn-primary" style="flex:1;justify-content:center;padding:8px;">Upgrade</a>
    <a href="/dashboard" class="btn btn-secondary" style="flex:1;justify-content:center;padding:8px;">Dashboard</a>
  </div>
</div>
