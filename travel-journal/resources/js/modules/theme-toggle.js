export function initThemeToggle() {
    const toggle = document.querySelector('[data-theme-toggle]');
    const root = document.documentElement;
    const storageKey = 'treep-theme';

    const emitThemeChange = (isDark) => {
        document.dispatchEvent(new CustomEvent('theme:changed', { detail: { isDark } }));
    };

    const updateIcon = (isDark) => {
        if (!toggle) return;
        toggle.textContent = isDark ? '☀️' : '🌙';
        toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    };

    const applyStored = () => {
        const stored = localStorage.getItem(storageKey) ?? localStorage.getItem('tripkit-theme');
        const prefersDark = stored ? stored === 'dark' : false;
        root.classList.toggle('dark', prefersDark);
        updateIcon(prefersDark);
        if (stored && !localStorage.getItem(storageKey)) {
            localStorage.setItem(storageKey, stored);
        }
        emitThemeChange(prefersDark);
    };

    applyStored();

    if (!toggle) return;

    toggle.addEventListener('click', () => {
        const willUseDark = !root.classList.contains('dark');
        root.classList.toggle('dark', willUseDark);
        localStorage.setItem(storageKey, willUseDark ? 'dark' : 'light');
        updateIcon(willUseDark);
        emitThemeChange(willUseDark);
    });
}
