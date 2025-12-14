export function initThemeToggle() {
    const toggle = document.querySelector('[data-theme-toggle]');
    const root = document.documentElement;

    const applyStored = () => {
        const stored = localStorage.getItem('tripkit-theme');
        if (stored === 'dark') {
            root.classList.add('dark');
        } else if (stored === 'light') {
            root.classList.remove('dark');
        }
    };

    applyStored();

    if (!toggle) return;

    toggle.addEventListener('click', () => {
        const willUseDark = !root.classList.contains('dark');
        root.classList.toggle('dark', willUseDark);
        localStorage.setItem('tripkit-theme', willUseDark ? 'dark' : 'light');
    });
}
