// PromptTai Tools - Main App JS
document.addEventListener('DOMContentLoaded', () => {
  // Mobile menu toggle
  const menuToggle = document.getElementById('menuToggle');
  const nav = document.querySelector('.nav');
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('mobile-open');
      const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
      menuToggle.setAttribute('aria-expanded', String(!expanded));
    });
  }

  // Toast helper
  window.showToast = (message, type = 'info') => {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  };

  // Global search
  const searchInput = document.getElementById('globalSearch');
  const searchResults = document.getElementById('searchResults');
  let searchTimeout;

  if (searchInput && searchResults) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      const q = searchInput.value.trim();
      if (q.length < 2) {
        searchResults.classList.remove('active');
        searchResults.innerHTML = '';
        return;
      }
      searchTimeout = setTimeout(async () => {
        try {
          const res = await fetch(`/api/tools/search.php?q=${encodeURIComponent(q)}`);
          const data = await res.json();
          if (data.tools && data.tools.length > 0) {
            searchResults.innerHTML = data.tools.map(tool => `
              <a href="/tools/${tool.slug}/" class="search-result-item">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#a5b4fc,#6366f1);border-radius:8px;display:grid;place-items:center;color:white;">${tool.name.charAt(0)}</div>
                <div>
                  <div style="font-weight:600;">${escapeHtml(tool.name)}</div>
                  <div style="font-size:0.8rem;color:#64748b;">${escapeHtml(tool.category_name || '')}</div>
                </div>
              </a>
            `).join('');
            searchResults.classList.add('active');
          } else {
            searchResults.innerHTML = '<div style="padding:16px;text-align:center;color:#64748b;">No tools found</div>';
            searchResults.classList.add('active');
          }
        } catch (e) {
          console.error('Search error', e);
        }
      }, 300);
    });

    // Close search on outside click
    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.classList.remove('active');
      }
    });

    // Keyboard navigation
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        searchResults.classList.remove('active');
      }
    });
  }

  // Usage indicator refresh
  async function refreshUsage() {
    try {
      const res = await fetch('/api/usage/status.php');
      const data = await res.json();
      const el = document.getElementById('usageIndicator');
      if (el && data) {
        if (data.has_subscription) {
          el.className = 'usage-indicator pro';
          el.innerHTML = '⭐ Pro Plan — Active';
        } else {
          el.className = 'usage-indicator' + (data.free_uses_remaining <= 2 ? ' low' : '');
          el.innerHTML = `Free: ${data.free_uses_remaining}/${data.free_uses_total}`;
        }
      }
    } catch (e) {}
  }
  refreshUsage();

  // Tool execution with usage gating
  window.executeTool = async (toolSlug, payload, onSuccess, onError) => {
    try {
      const res = await fetch('/api/tools/execute.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ tool_slug: toolSlug, ...payload })
      });
      const data = await res.json();
      if (res.status === 402 || data.error === 'upgrade_required') {
        // Show upgrade modal
        const modal = document.getElementById('upgradeModal');
        if (modal) {
          modal.classList.add('active');
        } else {
          window.showToast(data.message || "You've used all free uses. Please upgrade.", 'error');
          setTimeout(() => window.location.href = '/pricing', 1500);
        }
        if (onError) onError(data);
        return;
      }
      if (!res.ok) {
        throw new Error(data.error || 'Tool execution failed');
      }
      if (onSuccess) onSuccess(data);
      refreshUsage();
      // Analytics
      if (window.gtag) {
        gtag('event', 'tool_completed', { tool: toolSlug });
      }
    } catch (err) {
      console.error(err);
      if (onError) onError({ error: err.message });
      window.showToast(err.message, 'error');
    }
  };

  // Close modal on outside click
  document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.remove('active');
    });
  });
});

function escapeHtml(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
