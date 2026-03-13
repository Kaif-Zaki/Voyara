(function () {
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const navToggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('mobileNav');
    const navBackdrop = document.querySelector('[data-nav-backdrop]');
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

    if (navToggle && nav) {
        navToggle.addEventListener('click', () => {
            const isOpen = document.body.classList.toggle('nav-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        nav.querySelectorAll('a, button').forEach((item) => {
            item.addEventListener('click', () => {
                document.body.classList.remove('nav-open');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    if (navBackdrop && navToggle) {
        navBackdrop.addEventListener('click', () => {
            document.body.classList.remove('nav-open');
            navToggle.setAttribute('aria-expanded', 'false');
        });
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (!root.hasAttribute('data-theme')) {
            updateIcon();
        }
    });
})();
