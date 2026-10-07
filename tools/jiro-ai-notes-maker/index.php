<?php
// External AI Tool: JIRO-AI NOTES MAKER
?>
<div class="alert alert-info">
  <strong>External AI Tool</strong><br>
  Originally at <code>https://ai.earncolour.xyz/notes-maker-ai.html</code>. Migrated wrapper.
</div>
<div style="text-align:center;padding:20px;">
  <a href="https://ai.earncolour.xyz/notes-maker-ai.html" target="_blank" class="btn btn-primary">Open Original Tool</a>
  <a href="/category/ai-tools/" class="btn btn-secondary">Browse AI Tools</a>
</div>
<div class="card" style="margin-top:20px;">
  <h3>JIRO-AI NOTES MAKER — Coming Soon in Unified Platform</h3>
  <p>Will include unified login, 10 free uses, better performance.</p>
  <p style="color:var(--text-muted);font-size:0.9rem;">Category: 🤖 AI Tools</p>
</div>
<script>
fetch('/api/tools/execute.php', {
  method: 'POST',
  headers: {'Content-Type':'application/json','X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content},
  body: JSON.stringify({tool_slug: 'jiro-ai-notes-maker', input: 'external_view'})
}).then(r=>r.json()).then(d=>{});
</script>
