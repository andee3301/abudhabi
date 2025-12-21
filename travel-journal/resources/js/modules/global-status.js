export function initGlobalStatus() {
    const clockEl = document.querySelector('[data-global-clock]');

    if (clockEl) {
        const tz = clockEl.dataset.tz || 'UTC';
        const updateClock = () => {
            try {
                clockEl.textContent = new Date().toLocaleTimeString([], {
                    timeZone: tz,
                    hour: '2-digit',
                    minute: '2-digit',
                });
            } catch (e) {
                clockEl.textContent = new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                });
            }
        };

        updateClock();
        setInterval(updateClock, 1000);
    }
}
