<?php
// External AI Tool: AI Essay & Assignment Writer
?>
<div class="alert alert-info">
  <strong>External AI Tool</strong><br>
  Originally at <code>https://ai.jdiro.com//AI-Essay-Assignment-Writer//index.html</code>. Migrated wrapper.
</div>
<div style="text-align:center;padding:20px;">
  <a href="https://ai.jdiro.com//AI-Essay-Assignment-Writer//index.html" target="_blank" class="btn btn-primary">Open Original Tool</a>
  <a href="/category/ai-tools/" class="btn btn-secondary">Browse AI Tools</a>
</div>
<div class="card" style="margin-top:20px;">
  <h3>AI Essay & Assignment Writer — Coming Soon in Unified Platform</h3>
  <p>Will include unified login, 10 free uses, better performance.</p>
  <p style="color:var(--text-muted);font-size:0.9rem;">Category: 🤖 AI Tools</p>
</div>
<script>
fetch('/api/tools/execute.php', {
  method: 'POST',
  headers: {'Content-Type':'application/json','X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content},
  body: JSON.stringify({tool_slug: 'ai-essay-assignment-writer', input: 'external_view'})
}).then(r=>r.json()).then(d=>{});
</script>
