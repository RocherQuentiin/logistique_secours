import './bootstrap';

// Theme toggle: persists preference and updates on load
(() => {
  const root = document.documentElement;
  const storageKey = 'theme';
  const btnId = 'themeToggle';

  const applyTheme = (theme) => {
    if (theme === 'dark') {
      root.setAttribute('data-theme', 'dark');
    } else {
      root.removeAttribute('data-theme');
    }
  };

  const saved = localStorage.getItem(storageKey);
  if (saved) {
    applyTheme(saved);
  } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    applyTheme('dark');
  }

  window.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    const setIcon = () => {
      const isDark = root.getAttribute('data-theme') === 'dark';
      btn.textContent = isDark ? '🌙' : '☀️';
      btn.setAttribute('aria-label', isDark ? 'Passer en thème clair' : 'Passer en thème sombre');
      btn.title = isDark ? 'Thème sombre' : 'Thème clair';
    };
    setIcon();
    btn.addEventListener('click', () => {
      const isDark = root.getAttribute('data-theme') === 'dark';
      const next = isDark ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem(storageKey, next);
      setIcon();
    });
  });

  // If no saved preference, react to system preference changes
  if (!saved && window.matchMedia) {
    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    mq.addEventListener?.('change', (e) => {
      applyTheme(e.matches ? 'dark' : 'light');
      // Do not persist when following system changes without explicit user choice
    });
  }
})();

// Table search: filters table rows on input[data-table-search]
(() => {
  const filterTable = (el, q) => {
    const table = el?.tagName === 'TABLE' ? el : el?.closest('table');
    const term = q.trim().toLowerCase();
    const rows = table?.querySelectorAll('tbody tr');
    if (!rows) return;
    rows.forEach(tr => {
      const text = tr.textContent.toLowerCase();
      const match = term === '' || text.includes(term);
      tr.style.display = match ? '' : 'none';
    });
  };
  const wire = () => {
    document.querySelectorAll('input[data-table-search]').forEach(inp => {
      const selector = inp.getAttribute('data-target');
      const target = selector ? document.querySelector(selector) : inp.closest('.card')?.querySelector('table');
      if (!table) return;
      const handler = () => filterTable(target, inp.value || '');
      inp.addEventListener('input', handler);     // fires on each character
      inp.addEventListener('keyup', handler);     // fallback for some IMEs
      inp.addEventListener('search', handler);    // when user clears with X
    });
  };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wire);
  } else { wire(); }
})();
