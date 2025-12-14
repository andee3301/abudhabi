export function initThemeToggle() {
    const toggle = document.querySelector('[data-theme-toggle]');
    const root = document.documentElement;
    const storageKey = 'treep-theme';

    const updateIcon = (isDark) => {
        if (!toggle) return;
        toggle.textContent = isDark ? '☀️' : '🌙';
        toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    };

    const applyStored = () => {
        const stored = localStorage.getItem(storageKey) ?? localStorage.getItem('tripkit-theme');
        const prefersDark = stored === null ? true : stored === 'dark';
        root.classList.toggle('dark', prefersDark);
        updateIcon(prefersDark);
        if (stored && !localStorage.getItem(storageKey)) {
            localStorage.setItem(storageKey, stored);
        }
    };

    applyStored();

    if (!toggle) return;

    toggle.addEventListener('click', () => {
        const willUseDark = !root.classList.contains('dark');
        root.classList.toggle('dark', willUseDark);
        localStorage.setItem(storageKey, willUseDark ? 'dark' : 'light');
        updateIcon(willUseDark);
    });
}
