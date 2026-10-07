<?php
// Front controller for PromptTai Tools
// Enable error display if debug
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../app/config/config.php';
    Security::setSecurityHeaders();
    Auth::initSession();
} catch (Throwable $e) {
    // If config fails, show friendly error
    if (defined('APP_DEBUG') && APP_DEBUG) {
        http_response_code(500);
        echo "<h1>Config Error</h1><pre>" . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString()) . "</pre>";
        echo "<p>Check .env file exists and storage/ is writable (755)</p>";
        exit;
    } else {
        error_log("Config error: " . $e->getMessage());
        http_response_code(500);
        include __DIR__ . '/500.html';
        exit;
    }
}


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle static assets and direct files
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|webp|svg|ico|woff|woff2|ttf|map)$/', $uri)) {
    return false; // Let Apache serve
}

// Router
$router = new Router();

// Homepage
$router->get('/', function() {
    $user = Auth::user();
    $popular = ToolService::getPopular(12);
    $featured = ToolService::getFeatured(8);
    $categories = CategoryService::getAll();
    $pageTitle = "PromptTai Tools - Powerful Online Tools. One Simple Platform.";
    $pageDescription = "Free online tools for PDF, Images, Calculators, AI, Audio/Video. 181+ tools. 10 free uses, then Pro. Fast, secure, mobile-friendly.";
    $canonicalUrl = APP_DOMAIN . '/';
    $ogTags = Seo::ogTags($pageTitle, $pageDescription, $canonicalUrl);
    
    include TEMPLATE_PATH . '/header.php';
    ?>
    <section class="hero">
      <div>
        <h1>Powerful Online Tools.<br><span>One Simple Platform.</span></h1>
        <p>181+ free tools for PDF, Images, Calculators, AI, Finance, Health, Sports, and more. 10 free uses, unlimited with Pro. No signup required to try.</p>
        <div class="hero-actions">
          <a href="#tools" class="btn btn-primary">Explore Tools</a>
          <a href="/pricing" class="btn btn-secondary">View Pricing</a>
        </div>
        <div style="margin-top:20px;display:flex;gap:16px;font-size:0.9rem;color:var(--text-muted);">
          <span>✓ No credit card for free</span>
          <span>✓ Fast & secure</span>
          <span>✓ Mobile friendly</span>
        </div>
      </div>
      <div class="card" style="padding:0;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#6366f1,#0ea5e9);padding:32px;color:white;text-align:center;">
          <div style="font-size:3rem;margin-bottom:12px;">🛠️</div>
          <h3 style="margin:0 0 8px;">181+ Tools</h3>
          <p style="margin:0;opacity:0.9;">PDF, Image, Calculators, AI, and more</p>
        </div>
        <div style="padding:20px;">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;text-align:center;">
            <div><strong>10</strong><br><span style="font-size:0.8rem;color:var(--text-muted);">Free uses</span></div>
            <div><strong>∞</strong><br><span style="font-size:0.8rem;color:var(--text-muted);">With Pro</span></div>
            <div><strong>15+</strong><br><span style="font-size:0.8rem;color:var(--text-muted);">Categories</span></div>
            <div><strong>24/7</strong><br><span style="font-size:0.8rem;color:var(--text-muted);">Available</span></div>
          </div>
        </div>
      </div>
    </section>

    <?= AdService::adTop($user) ?>

    <section id="tools">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">🔥 Popular Tools</h2>
        <a href="/category/all/" class="btn btn-secondary" style="padding:8px 14px;">View All</a>
      </div>
      <div class="tool-grid">
        <?php foreach ($popular as $tool): ?>
          <?php include TEMPLATE_PATH . '/components/tool-card.php'; ?>
        <?php endforeach; ?>
      </div>
    </section>

    <section style="margin-top:40px;">
      <h2>📚 Categories</h2>
      <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
          <a href="/category/<?= e($cat['slug']) ?>/" class="category-card">
            <div class="category-card-icon"><?= e($cat['icon'] ?? '📁') ?></div>
            <h3><?= e($cat['name']) ?></h3>
            <p><?= $cat['tool_count'] ?? 0 ?> tools</p>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <section style="margin-top:40px;">
      <h2>⭐ Featured AI Tools</h2>
      <div class="tool-grid">
        <?php foreach ($featured as $tool): ?>
          <?php include TEMPLATE_PATH . '/components/tool-card.php'; ?>
        <?php endforeach; ?>
      </div>
    </section>

    <section style="margin-top:40px;" class="card">
      <h2>Why PromptTai Tools?</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:16px;">
        <div><strong>🚀 Fast & Lightweight</strong><p style="color:var(--text-muted);margin:8px 0 0;font-size:0.9rem;">Optimized for speed, no heavy frameworks. Works on mobile and desktop.</p></div>
        <div><strong>🔒 Secure & Private</strong><p style="color:var(--text-muted);margin:8px 0 0;font-size:0.9rem;">Files processed securely, auto-deleted. No data selling.</p></div>
        <div><strong>🎯 Unified Experience</strong><p style="color:var(--text-muted);margin:8px 0 0;font-size:0.9rem;">One design, one login, one subscription for all tools.</p></div>
        <div><strong>💳 Fair Pricing</strong><p style="color:var(--text-muted);margin:8px 0 0;font-size:0.9rem;">10 free uses, then affordable Pro. No hidden fees.</p></div>
      </div>
    </section>

    <section style="margin-top:40px;text-align:center;" class="card">
      <h2>Ready to get started?</h2>
      <p style="color:var(--text-muted);">Join thousands using PromptTai Tools daily</p>
      <div style="margin-top:20px;">
        <a href="/register" class="btn btn-primary">Get Started Free</a>
        <a href="/pricing" class="btn btn-secondary">View Pricing</a>
      </div>
    </section>

    <?= AdService::adBottom($user) ?>

    <?php
    include TEMPLATE_PATH . '/footer.php';
});

// Tool page
$router->get('/tools/{slug}', function($params) {
    $slug = $params['slug'];
    $tool = ToolService::getBySlug($slug);
    if (!$tool) {
        // Check alias
        $db = Database::getInstance();
        $alias = $db->fetchOne("SELECT tool_id FROM tool_aliases WHERE alias = ?", [$slug]);
        if ($alias) {
            $tool = $db->fetchOne("SELECT t.*, c.name as category_name, c.slug as category_slug FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = ?", [$alias['tool_id']]);
        }
    }
    if (!$tool) {
        http_response_code(404);
        include TEMPLATE_PATH . '/404.php';
        return;
    }

    $user = Auth::user();
    ToolService::incrementView($tool['id']);
    $related = ToolService::getRelated($tool['id'], $tool['category_id'] ?? 0, 6);
    $faqs = [
        ['q' => "How to use {$tool['name']}?", 'a' => "Enter your data, click process, and download result. Free users get 10 uses."],
        ['q' => "Is {$tool['name']} free?", 'a' => "Yes, 10 free uses per account, then Pro for unlimited."],
        ['q' => "Is my data secure?", 'a' => "Yes, files are processed securely and auto-deleted. We don't store your data."]
    ];

    // Load tool-specific workspace if exists
    $toolWorkspacePath = ROOT_PATH . '/tools/' . $tool['slug'] . '/index.php';
    $toolContent = '';
    if (file_exists($toolWorkspacePath)) {
        ob_start();
        include $toolWorkspacePath;
        $toolContent = ob_get_clean();
    } else {
        // Generic tool placeholder - will be replaced by migrated tools
        $toolContent = '
        <div class="form-group">
          <label class="form-label">Input</label>
          <textarea class="form-textarea" id="toolInput" placeholder="Enter your data here..."></textarea>
        </div>
        <button class="btn btn-primary" onclick="processTool()">Process</button>
        <script>
          function processTool() {
            const input = document.getElementById("toolInput").value;
            if (!input) { showToast("Please enter input", "error"); return; }
            executeTool("' . $tool['slug'] . '", {input}, (data) => {
              document.getElementById("toolResult").style.display = "block";
              document.getElementById("resultContent").innerText = data.result || "Processed: " + input.substring(0,100);
            });
          }
        </script>
        ';
    }

    include TEMPLATE_PATH . '/tool-layout.php';
});

// Category page
$router->get('/category/{slug}', function($params) {
    $slug = $params['slug'];
    $user = Auth::user();
    if ($slug === 'all') {
        $category = ['name' => 'All Tools', 'slug' => 'all', 'description' => 'All 181+ tools in one place'];
        $tools = ToolService::getAllActive();
    } else {
        $category = CategoryService::getBySlug($slug);
        if (!$category) {
            http_response_code(404);
            include TEMPLATE_PATH . '/404.php';
            return;
        }
        $tools = ToolService::getByCategory($category['id']);
    }

    $pageTitle = $category['name'] . ' - Free Online Tools | ' . APP_NAME;
    $pageDescription = $category['description'] ?? "Free {$category['name']} tools";
    $canonicalUrl = APP_DOMAIN . '/category/' . $category['slug'] . '/';
    $ogTags = Seo::ogTags($pageTitle, $pageDescription, $canonicalUrl);

    include TEMPLATE_PATH . '/header.php';
    ?>
    <div class="breadcrumbs">
      <a href="/">Home</a> <span>›</span> <span><?= e($category['name']) ?></span>
    </div>
    <h1><?= e($category['name']) ?> <span style="color:var(--text-muted);font-weight:500;font-size:1rem;">(<?= count($tools) ?> tools)</span></h1>
    <p style="color:var(--text-muted);"><?= e($category['description'] ?? '') ?></p>

    <?= AdService::adTop($user) ?>

    <div class="tool-grid" style="margin-top:20px;">
      <?php foreach ($tools as $tool): ?>
        <?php include TEMPLATE_PATH . '/components/tool-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <?php if (empty($tools)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <h3>No tools found in this category</h3>
        <p>Try another category or search</p>
      </div>
    <?php endif; ?>

    <?= AdService::adBottom($user) ?>
    <?php
    include TEMPLATE_PATH . '/footer.php';
});

// Pricing
$router->get('/pricing', function() {
    $user = Auth::user();
    $plans = SubscriptionService::getPlans(true);
    $pageTitle = "Pricing - " . APP_NAME;
    $pageDescription = "Free 10 uses, Pro Monthly ₹299, Pro Yearly ₹1999. Unlimited tools, priority support, ad-free.";
    $canonicalUrl = APP_DOMAIN . '/pricing';
    $ogTags = Seo::ogTags($pageTitle, $pageDescription, $canonicalUrl);
    include TEMPLATE_PATH . '/header.php';
    ?>
    <div class="text-center" style="max-width:700px;margin:0 auto 40px;">
      <h1>Simple, Fair Pricing</h1>
      <p style="color:var(--text-muted);">Start free with 10 uses. Upgrade to Pro for unlimited access to all 181+ tools.</p>
    </div>

    <div class="pricing-grid">
      <?php foreach ($plans as $plan): 
        $features = json_decode($plan['features'] ?? '[]', true) ?: [];
      ?>
        <div class="pricing-card <?= $plan['slug']==='pro-monthly' ? 'featured' : '' ?>">
          <h3><?= e($plan['name']) ?></h3>
          <p style="color:var(--text-muted);font-size:0.9rem;"><?= e($plan['description']) ?></p>
          <div class="pricing-price">
            <?php if ($plan['amount'] == 0): ?>
              Free
            <?php else: ?>
              ₹<?= number_format($plan['amount']) ?><span>/<?= e($plan['billing_interval']) ?></span>
            <?php endif; ?>
          </div>
          <ul class="pricing-features">
            <?php foreach ($features as $feat): ?>
              <li><?= e($feat) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php if ($plan['amount'] == 0): ?>
            <a href="/register" class="btn btn-secondary" style="width:100%;justify-content:center;">Get Started</a>
          <?php else: ?>
            <button onclick="startCheckout(<?= $plan['id'] ?>)" class="btn btn-primary" style="width:100%;justify-content:center;">Upgrade to <?= e($plan['name']) ?></button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="margin-top:40px;" class="card">
      <h3>FAQ</h3>
      <dl>
        <dt style="font-weight:700;margin-top:12px;">What counts as a use?</dt>
        <dd style="color:var(--text-muted);">Each successful tool execution (calculate, convert, compress, etc) counts as one use. Failed attempts don't count.</dd>
        <dt style="font-weight:700;margin-top:12px;">Can I cancel anytime?</dt>
        <dd style="color:var(--text-muted);">Yes, cancel anytime from Account > Billing. You keep Pro until period end.</dd>
        <dt style="font-weight:700;margin-top:12px;">What payment methods?</dt>
        <dd style="color:var(--text-muted);">Razorpay supports UPI, Cards, NetBanking, Wallets. Stripe coming soon.</dd>
      </dl>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
      async function startCheckout(planId) {
        <?php if (!$user): ?>
          window.location.href = '/register?redirect=/pricing';
          return;
        <?php endif; ?>
        try {
          const res = await fetch('/api/billing/create-order.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({plan_id: planId})
          });
          const data = await res.json();
          if (!res.ok) throw new Error(data.error || 'Failed to create order');
          
          const options = {
            key: data.key_id,
            amount: data.amount,
            currency: data.currency,
            name: 'PromptTai Tools',
            description: data.plan_name,
            order_id: data.order_id,
            handler: async function (response) {
              const verifyRes = await fetch('/api/billing/verify.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({
                  razorpay_payment_id: response.razorpay_payment_id,
                  razorpay_order_id: response.razorpay_order_id,
                  razorpay_signature: response.razorpay_signature,
                  plan_id: planId
                })
              });
              const verifyData = await verifyRes.json();
              if (verifyRes.ok) {
                showToast('Payment successful! Pro activated.', 'success');
                setTimeout(() => window.location.href = '/dashboard', 1500);
              } else {
                showToast(verifyData.error || 'Payment verification failed', 'error');
              }
            },
            prefill: { name: '<?= e($user['name'] ?? '') ?>', email: '<?= e($user['email'] ?? '') ?>' },
            theme: { color: '#6366f1' }
          };
          const rzp = new Razorpay(options);
          rzp.open();
        } catch (e) {
          showToast(e.message, 'error');
        }
      }
    </script>
    <?php
    include TEMPLATE_PATH . '/footer.php';
});

// Auth routes
$router->get('/login', function() {
    if (Auth::check()) { redirect('/dashboard'); }
    $pageTitle = "Login - " . APP_NAME;
    include TEMPLATE_PATH . '/header.php';
    ?>
    <div style="max-width:400px;margin:40px auto;" class="card">
      <h2 style="margin:0 0 20px;text-align:center;">Welcome back</h2>
      <?php if (!empty($_GET['error'])): ?><div class="alert alert-error"><?= e($_GET['error']) ?></div><?php endif; ?>
      <form method="POST" action="/api/auth/login.php">
        <?= Csrf::tokenField() ?>
        <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email" name="email" required></div>
        <div class="form-group"><label class="form-label">Password</label><input class="form-input" type="password" name="password" required></div>
        <button class="btn btn-primary" style="width:100%;justify-content:center;">Login</button>
      </form>
      <p style="text-align:center;margin-top:16px;font-size:0.9rem;">No account? <a href="/register">Register</a> | <a href="/forgot-password">Forgot password?</a></p>
    </div>
    <?php include TEMPLATE_PATH . '/footer.php';
});

$router->get('/register', function() {
    if (Auth::check()) { redirect('/dashboard'); }
    $pageTitle = "Register - " . APP_NAME;
    include TEMPLATE_PATH . '/header.php';
    ?>
    <div style="max-width:400px;margin:40px auto;" class="card">
      <h2 style="margin:0 0 8px;text-align:center;">Create account</h2>
      <p style="text-align:center;color:var(--text-muted);font-size:0.9rem;margin:0 0 20px;">Get 10 free uses instantly</p>
      <?php if (!empty($_GET['error'])): ?><div class="alert alert-error"><?= e($_GET['error']) ?></div><?php endif; ?>
      <form method="POST" action="/api/auth/register.php">
        <?= Csrf::tokenField() ?>
        <div class="form-group"><label class="form-label">Name</label><input class="form-input" type="text" name="name" required></div>
        <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email" name="email" required></div>
        <div class="form-group"><label class="form-label">Password</label><input class="form-input" type="password" name="password" required><small style="color:var(--text-muted);">Min 8 chars, uppercase, lowercase, number</small></div>
        <button class="btn btn-primary" style="width:100%;justify-content:center;">Create Account</button>
      </form>
      <p style="text-align:center;margin-top:16px;font-size:0.9rem;">Have account? <a href="/login">Login</a></p>
    </div>
    <?php include TEMPLATE_PATH . '/footer.php';
});

$router->get('/logout', function() {
    Auth::logout();
    redirect('/');
});

$router->get('/dashboard', function() {
    Auth::requireLogin();
    $user = Auth::user();
    $usage = UsageService::getUsageStatus($user);
    $recentUsage = Database::getInstance()->fetchAll("SELECT tu.*, t.name as tool_name, t.slug FROM tool_usage tu JOIN tools t ON tu.tool_id = t.id WHERE tu.user_id = ? ORDER BY tu.created_at DESC LIMIT 10", [$user['id']]);
    $sub = SubscriptionService::getUserSubscription($user['id']);
    $pageTitle = "Dashboard - " . APP_NAME;
    include TEMPLATE_PATH . '/header.php';
    ?>
    <h1>Dashboard</h1>
    <p style="color:var(--text-muted);">Welcome back, <?= e($user['name']) ?></p>
    
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin:20px 0;">
      <div class="card"><h4 style="margin:0;">Free Uses</h4><div style="font-size:2rem;font-weight:800;"><?= $usage['free_uses_remaining'] ?>/<?= $usage['free_uses_total'] ?></div></div>
      <div class="card"><h4 style="margin:0;">Subscription</h4><div style="font-size:1.2rem;font-weight:700;margin-top:8px;"><?= $usage['has_subscription'] ? 'Pro Active' : 'Free Plan' ?></div></div>
      <div class="card"><h4 style="margin:0;">Total Uses</h4><div style="font-size:2rem;font-weight:800;"><?= $usage['free_uses_used'] ?></div></div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;">
      <div>
        <h3>Recent Activity</h3>
        <div class="card" style="padding:0;">
          <?php if (empty($recentUsage)): ?>
            <div style="padding:20px;text-align:center;color:var(--text-muted);">No activity yet. Try a tool!</div>
          <?php else: ?>
            <?php foreach ($recentUsage as $u): ?>
              <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                <span><a href="/tools/<?= e($u['slug']) ?>/"><?= e($u['tool_name']) ?></a> — <?= e($u['usage_type']) ?></span>
                <span style="color:var(--text-muted);font-size:0.85rem;"><?= date('M d', strtotime($u['created_at'])) ?></span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div>
        <?php include TEMPLATE_PATH . '/components/usage-indicator.php'; ?>
        <?php if ($sub): ?>
        <div class="card" style="margin-top:16px;">
          <h4>Subscription</h4>
          <p><?= e($sub['plan_name']) ?> — <?= e($sub['status']) ?></p>
          <p style="font-size:0.85rem;color:var(--text-muted);">Ends: <?= e($sub['current_period_end']) ?></p>
          <a href="/account/billing" class="btn btn-secondary" style="width:100%;justify-content:center;margin-top:12px;">Manage Billing</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php include TEMPLATE_PATH . '/footer.php';
});

$router->get('/account', function() {
    Auth::requireLogin();
    $user = Auth::user();
    $pageTitle = "Account - " . APP_NAME;
    include TEMPLATE_PATH . '/header.php';
    ?>
    <h1>Account</h1>
    <div class="card" style="max-width:600px;">
      <p><strong>Name:</strong> <?= e($user['name']) ?></p>
      <p><strong>Email:</strong> <?= e($user['email']) ?></p>
      <p><strong>Role:</strong> <?= e($user['role']) ?></p>
      <p><strong>Member since:</strong> <?= e($user['created_at']) ?></p>
      <div style="margin-top:20px;">
        <a href="/account/billing" class="btn btn-primary">Billing</a>
        <a href="/logout" class="btn btn-secondary">Logout</a>
      </div>
    </div>
    <?php include TEMPLATE_PATH . '/footer.php';
});

$router->get('/account/billing', function() {
    Auth::requireLogin();
    $user = Auth::user();
    $sub = SubscriptionService::getUserSubscription($user['id']);
    $payments = SubscriptionService::getUserPayments($user['id']);
    $pageTitle = "Billing - " . APP_NAME;
    include TEMPLATE_PATH . '/header.php';
    ?>
    <h1>Billing</h1>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
      <div class="card">
        <h3>Current Plan</h3>
        <?php if ($sub): ?>
          <p><strong><?= e($sub['plan_name']) ?></strong> — <?= e($sub['status']) ?></p>
          <p>Amount: ₹<?= e($sub['amount']) ?> / <?= e($sub['billing_interval']) ?></p>
          <p>Current period: <?= e($sub['current_period_start']) ?> to <?= e($sub['current_period_end']) ?></p>
          <?php if ($sub['status'] === 'active'): ?>
            <form method="POST" action="/api/billing/cancel.php" onsubmit="return confirm('Cancel subscription?')">
              <?= Csrf::tokenField() ?>
              <button class="btn btn-secondary">Cancel Subscription</button>
            </form>
          <?php endif; ?>
        <?php else: ?>
          <p>Free plan — 10 free uses</p>
          <a href="/pricing" class="btn btn-primary">Upgrade</a>
        <?php endif; ?>
      </div>
      <div class="card">
        <h3>Payment History</h3>
        <?php if (empty($payments)): ?>
          <p style="color:var(--text-muted);">No payments yet</p>
        <?php else: ?>
          <?php foreach ($payments as $p): ?>
            <div style="padding:8px 0;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
              <span>₹<?= e($p['amount']) ?> — <?= e($p['status']) ?></span>
              <span style="color:var(--text-muted);font-size:0.85rem;"><?= date('M d, Y', strtotime($p['created_at'])) ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <?php include TEMPLATE_PATH . '/footer.php';
});

// Sitemap
$router->get('/sitemap.xml', function() {
    header('Content-Type: application/xml');
    $tools = ToolService::getAllActive();
    $categories = CategoryService::getAll();
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <url><loc>' . APP_DOMAIN . '/</loc><lastmod>' . date('Y-m-d') . '</lastmod><priority>1.0</priority></url>' . "\n";
    echo '  <url><loc>' . APP_DOMAIN . '/pricing</loc><lastmod>' . date('Y-m-d') . '</lastmod><priority>0.8</priority></url>' . "\n";
    foreach ($categories as $cat) {
        echo '  <url><loc>' . APP_DOMAIN . '/category/' . $cat['slug'] . '/</loc><lastmod>' . date('Y-m-d') . '</lastmod><priority>0.7</priority></url>' . "\n";
    }
    foreach ($tools as $tool) {
        echo '  <url><loc>' . APP_DOMAIN . '/tools/' . $tool['slug'] . '/</loc><lastmod>' . date('Y-m-d', strtotime($tool['updated_at'] ?? 'now')) . '</lastmod><priority>0.6</priority></url>' . "\n";
    }
    echo '</urlset>';
});

// Robots
$router->get('/robots.txt', function() {
    header('Content-Type: text/plain');
    echo "User-agent: *\n";
    echo "Allow: /\n";
    echo "Disallow: /admin\n";
    echo "Disallow: /api/\n";
    echo "Disallow: /account/\n";
    echo "Sitemap: " . APP_DOMAIN . "/sitemap.xml\n";
});

// Admin
$router->get('/admin', function() {
    Auth::requireAdmin();
    $stats = AdminService::getDashboardStats();
    $pageTitle = "Admin Dashboard";
    include TEMPLATE_PATH . '/header.php';
    ?>
    <h1>Admin Dashboard</h1>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin:20px 0;">
      <div class="card"><strong><?= $stats['total_users'] ?></strong><br>Total Users</div>
      <div class="card"><strong><?= $stats['active_users'] ?></strong><br>Active Users</div>
      <div class="card"><strong><?= $stats['total_tools'] ?></strong><br>Total Tools</div>
      <div class="card"><strong><?= $stats['total_subscriptions'] ?></strong><br>Active Subs</div>
      <div class="card"><strong>₹<?= number_format($stats['total_revenue']) ?></strong><br>Revenue</div>
      <div class="card"><strong><?= $stats['today_usage'] ?></strong><br>Today Usage</div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
      <div class="card">
        <h3>Popular Tools</h3>
        <?php foreach ($stats['popular_tools'] as $t): ?>
          <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border);">
            <span><?= e($t['name']) ?></span><span><?= $t['usage_count'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="card">
        <h3>Recent Users</h3>
        <?php foreach ($stats['recent_users'] as $u): ?>
          <div style="padding:6px 0;border-bottom:1px solid var(--border);">
            <?= e($u['name']) ?> — <?= e($u['email']) ?><br><small style="color:var(--text-muted);"><?= e($u['created_at']) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div style="margin-top:20px;">
      <a href="/admin/users" class="btn btn-secondary">Users</a>
      <a href="/admin/tools" class="btn btn-secondary">Tools</a>
      <a href="/admin/categories" class="btn btn-secondary">Categories</a>
      <a href="/admin/plans" class="btn btn-secondary">Plans</a>
    </div>
    <?php include TEMPLATE_PATH . '/footer.php';
});

// Dispatch with error handling
try {
    $router->dispatch();
} catch (Throwable $e) {
    error_log("Router dispatch error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    if (defined('APP_DEBUG') && APP_DEBUG) {
        http_response_code(500);
        echo "<h1>500 - Internal Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile() . ":" . $e->getLine()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "<hr><p>Check: .env DB config, storage/ permissions 755, PHP version 8.2, mod_rewrite enabled</p>";
        exit;
    } else {
        http_response_code(500);
        // Try to show 500.html, fallback to simple message
        if (file_exists(__DIR__ . '/500.html')) {
            include __DIR__ . '/500.html';
        } else {
            echo "<h1>500 Internal Server Error</h1><p>Please check error logs. If just uploaded, ensure .env exists and storage/ writable.</p>";
        }
        exit;
    }
}

