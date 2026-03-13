(function () {
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const stored = localStorage.getItem('voyara-theme');

    function applyTheme(theme) {
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
        } else if (theme === 'light') {
            root.setAttribute('data-theme', 'light');
        } else {
            root.removeAttribute('data-theme');
        }
        updateIcon();
    }

    function updateIcon() {
        const isDark = root.getAttribute('data-theme') === 'dark' ||
            (!root.hasAttribute('data-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        const sun = toggle ? toggle.querySelector('[data-theme-icon="sun"]') : null;
        const moon = toggle ? toggle.querySelector('[data-theme-icon="moon"]') : null;
        if (sun && moon) {
            sun.style.display = isDark ? 'none' : 'inline-flex';
            moon.style.display = isDark ? 'inline-flex' : 'none';
        }
    }

    if (stored) {
        applyTheme(stored);
    } else {
        updateIcon();
    }

    if (toggle) {
        toggle.addEventListener('click', () => {
            const current = root.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('voyara-theme', next);
            updateIcon();
        });
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (!root.hasAttribute('data-theme')) {
            updateIcon();
        }
    });
})();
