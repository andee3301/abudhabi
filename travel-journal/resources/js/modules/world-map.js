const projectPoint = (lat, lng, width, height) => {
    const x = ((lng + 180) / 360) * width;
    const y = ((90 - lat) / 180) * height;

    return { x, y };
};

const parsePoints = (payload) => {
    try {
        return JSON.parse(payload || '[]');
    } catch (e) {
        console.warn('Failed to parse map points', e);
        return [];
    }
};

const highlightCard = (tripId, active) => {
    const card = document.querySelector(`[data-trip-card="${tripId}"]`);
    if (!card) return;
    card.classList.toggle('map-highlight', active);
};

export function initWorldMap() {
    document.querySelectorAll('[data-world-map]').forEach(async (container) => {
        const src = container.dataset.mapSrc;
        const points = parsePoints(container.dataset.mapPoints);

        if (!src || !points.length) {
            container.innerHTML = '<div class="grid h-full place-items-center text-xs text-slate-500 dark:text-slate-400">No locations to plot yet.</div>';
            return;
        }

        try {
            const res = await fetch(src, { cache: 'force-cache' });
            const svgMarkup = await res.text();
            container.innerHTML = svgMarkup;
        } catch (e) {
            console.error('Map SVG failed to load', e);
            container.innerHTML = '<div class="grid h-full place-items-center text-xs text-slate-500 dark:text-slate-400">Map unavailable.</div>';
            return;
        }

        const svg = container.querySelector('svg');
        if (!svg) return;

        const viewBox = (svg.getAttribute('viewBox') || '0 0 960 480').split(' ').map(Number);
        const width = viewBox[2] || 960;
        const height = viewBox[3] || 480;

        const pinsGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        pinsGroup.setAttribute('data-map-pins', '');
        svg.appendChild(pinsGroup);

        points.forEach((point) => {
            const { x, y } = projectPoint(point.lat, point.lng, width, height);
            const pin = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            pin.setAttribute('cx', x.toFixed(2));
            pin.setAttribute('cy', y.toFixed(2));
            pin.setAttribute('r', '4.5');
            pin.setAttribute('fill', '#22c55e');
            pin.setAttribute('stroke', '#0f172a');
            pin.setAttribute('stroke-width', '1.2');
            pin.setAttribute('opacity', '0.82');
            pin.style.cursor = 'pointer';
            pin.dataset.pinId = point.id;

            const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
            title.textContent = point.title || 'Journey';
            pin.appendChild(title);

            pin.addEventListener('mouseenter', () => {
                pin.setAttribute('r', '6');
                pin.setAttribute('opacity', '1');
                highlightCard(point.id, true);
            });

            pin.addEventListener('mouseleave', () => {
                pin.setAttribute('r', '4.5');
                pin.setAttribute('opacity', '0.82');
                highlightCard(point.id, false);
            });

            pin.addEventListener('click', () => {
                if (point.url) {
                    window.location.href = point.url;
                }
            });

            pinsGroup.appendChild(pin);
        });
    });
}
