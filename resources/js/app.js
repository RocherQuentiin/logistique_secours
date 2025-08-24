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
