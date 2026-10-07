<?php
// Footer
?>
  </main>
  
  <footer class="footer">
    <div class="footer-inner">
      <div>
        <h4>PromptTai Tools</h4>
        <p style="color:var(--text-muted);font-size:0.9rem;">Powerful Online Tools. One Simple Platform. Fast, secure, and free to start.</p>
        <p style="margin-top:12px;">
          <a href="/">Home</a>
          <a href="/about">About</a>
          <a href="/contact">Contact</a>
        </p>
      </div>
      <div>
        <h4>Categories</h4>
        <a href="/category/pdf-tools/">PDF Tools</a>
        <a href="/category/image-tools/">Image Tools</a>
        <a href="/category/calculators/">Calculators</a>
        <a href="/category/ai-tools/">AI Tools</a>
        <a href="/category/audio-video-tools/">Audio & Video</a>
      </div>
      <div>
        <h4>Popular Tools</h4>
        <a href="/tools/pdf-merger/">PDF Merger</a>
        <a href="/tools/image-compressor/">Image Compressor</a>
        <a href="/tools/age-calculator/">Age Calculator</a>
        <a href="/tools/bmi-calculator/">BMI Calculator</a>
        <a href="/tools/word-to-pdf/">Word to PDF</a>
      </div>
      <div>
        <h4>Company</h4>
        <a href="/pricing">Pricing</a>
        <a href="/privacy-policy">Privacy Policy</a>
        <a href="/terms">Terms of Service</a>
        <a href="/contact">Contact Us</a>
        <a href="/sitemap.xml">Sitemap</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> PromptTai Tools — Built with ❤️ | Old domain <a href="https://jdiro.com/">jdiro.com</a> migrated to <a href="https://tool.prompttai.com/">tool.prompttai.com</a></p>
    </div>
  </footer>

  <!-- Upgrade Modal -->
  <div id="upgradeModal" class="modal" aria-hidden="true">
    <div class="modal-content">
      <h3>🚀 You've used all <?= FREE_USES_DEFAULT ?> free uses</h3>
      <p>Upgrade to Pro for unlimited access to all 181+ tools, priority support, and ad-free experience.</p>
      <div style="display:flex;gap:12px;justify-content:center;margin-top:20px;">
        <a href="/pricing" class="btn btn-primary">View Plans</a>
        <a href="/dashboard" class="btn btn-secondary">Go to Dashboard</a>
      </div>
      <button onclick="document.getElementById('upgradeModal').classList.remove('active')" style="margin-top:16px;background:none;border:none;color:var(--text-muted);cursor:pointer;">Maybe later</button>
    </div>
  </div>

  <script src="/assets/js/app.js"></script>
  <?php if (!empty($extraJs)): echo $extraJs; endif; ?>
</body>
</html>
