import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const parsePoints = (payload) => {
    try {
        return JSON.parse(payload || '[]');
    } catch (e) {
        console.warn('Unable to parse map points', e);
        return [];
    }
};

const themeFromDom = () => (document.documentElement.classList.contains('dark') ? 'dark' : 'light');

const createTileLayers = () => ({
    light: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 8,
        attribution: '&copy; OpenStreetMap contributors',
    }),
    dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png', {
        maxZoom: 8,
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
    }),
});

const buildMap = (container) => {
    const points = parsePoints(container.dataset.mapPoints);

    if (!points.length) {
        container.innerHTML = '<div class="grid h-full place-items-center text-xs text-slate-500 dark:text-slate-400">No journeys to plot yet.</div>';
        return;
    }

    container.innerHTML = '';

    const tiles = createTileLayers();
    const map = L.map(container, {
        worldCopyJump: true,
        zoomControl: true,
        attributionControl: true,
    });

    let currentTile = tiles[themeFromDom()] ?? tiles.light;
    currentTile.addTo(map);

    const markers = L.layerGroup().addTo(map);
    const bounds = [];

    points.forEach((point) => {
        if (point.lat === undefined || point.lng === undefined || point.lat === null || point.lng === null) {
            return;
        }

        const lat = Number(point.lat);
        const lng = Number(point.lng);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        const marker = L.circleMarker([lat, lng], {
            radius: 6,
            color: '#0ea5e9',
            weight: 2,
            fillColor: '#22d3ee',
            fillOpacity: 0.75,
        }).addTo(markers);

        marker.bindTooltip(point.title || 'Journey', { direction: 'top', offset: [0, -2] });

        if (point.url) {
            marker.on('click', () => {
                window.location.href = point.url;
            });
        }

        bounds.push([lat, lng]);
    });

    if (bounds.length) {
        map.fitBounds(bounds, { padding: [26, 26], maxZoom: 6 });
    } else {
        map.setView([20, 0], 2);
    }

    const setBaseLayer = (useDark) => {
        const next = tiles[useDark ? 'dark' : 'light'];
        if (!next || next === currentTile) {
            return;
        }
        map.removeLayer(currentTile);
        currentTile = next;
        currentTile.addTo(map);
    };

    document.addEventListener('theme:changed', (event) => {
        setBaseLayer(!!event.detail?.isDark);
    });
};

const initPastToggle = () => {
    const toggle = document.querySelector('[data-past-toggle]');
    const extras = Array.from(document.querySelectorAll('[data-past-extra]'));

    if (!toggle || extras.length === 0) {
        return;
    }

    const collapsedLabel = toggle.dataset.collapsedLabel || toggle.textContent.trim() || 'Show full history';
    const expandedLabel = toggle.dataset.expandedLabel || 'Hide history';

    const setExpanded = (state) => {
        extras.forEach((card) => card.classList.toggle('hidden', !state));
        toggle.dataset.expanded = state ? 'true' : 'false';
        toggle.textContent = state ? expandedLabel : collapsedLabel;
    };

    setExpanded(false);

    toggle.addEventListener('click', () => {
        setExpanded(toggle.dataset.expanded !== 'true');
    });
};

export function initJourneyDashboard() {
    document.querySelectorAll('[data-journey-map]').forEach((container) => buildMap(container));
    initPastToggle();
}
