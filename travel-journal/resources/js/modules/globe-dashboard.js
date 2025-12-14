import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const parseJson = (value, fallback = null) => {
    if (!value) return fallback;
    try {
        return JSON.parse(value);
    } catch (e) {
        console.warn('Failed parsing JSON payload', e);
        return fallback;
    }
};

export function initGlobeDashboard() {
    const root = document.querySelector('[data-globe-dashboard]');
    if (!root) return;

    const mapContainer = root.querySelector('[data-globe]');
    const searchInput = root.querySelector('[data-city-search]');
    const suggestions = root.querySelector('[data-city-suggestions]');
    const loader = root.querySelector('[data-city-loading]');
    const fallback = root.querySelector('[data-globe-fallback]');

    const intelTemplate = root.dataset.intelTemplate;
    const searchEndpoint = root.dataset.searchEndpoint;
    const initialPayload = parseJson(root.dataset.initial, {});
    const featuredCities = parseJson(root.dataset.featured, []);

    let mapInstance;
    let markerLayer;
    let markers = [];

    const intelUrl = (slug) => intelTemplate.replace('__slug__', slug);
    const setLoading = (state) => {
        if (!loader) return;
        loader.classList.toggle('hidden', !state);
    };

    const setText = (selector, value) => {
        root.querySelectorAll(selector).forEach((el) => {
            el.textContent = value || '—';
        });
    };

    const renderList = (selector, items, formatter) => {
        const el = root.querySelector(selector);
        if (!el) return;
        el.innerHTML = '';
        if (!items || !items.length) {
            const li = document.createElement('li');
            li.className = 'text-sm text-slate-500 dark:text-slate-400';
            li.textContent = 'No data yet';
            el.appendChild(li);
            return;
        }
        items.forEach((item) => {
            const li = document.createElement('li');
            li.className = 'flex items-start gap-2 text-sm text-slate-800 dark:text-slate-200';
            li.innerHTML = formatter(item);
            el.appendChild(li);
        });
    };

    const formatTime = (value, timezone) => {
        if (!value) return '—';
        const date = new Date(value);
        return new Intl.DateTimeFormat(undefined, {
            hour: '2-digit',
            minute: '2-digit',
            timeZone: timezone || 'UTC',
        }).format(date);
    };

    const updatePanel = (payload = {}) => {
        const city = payload.city || {};
        const intel = payload.intel || {};
        const time = payload.time || {};
        const budget = payload.budget || intel.budget || {};
        const electrical = payload.electrical || {};
        const emergencies = payload.emergency_contacts || intel.emergency_numbers || [];

        setText('[data-city-field="name"]', city.name || 'Choose a city');
        setText('[data-city-field="tagline"]', intel.tagline || 'Glassy globe intel');
        setText('[data-city-field="summary"]', intel.summary);
        setText('[data-city-field="timezone"]', city.timezone || '—');
        setText('[data-city-field="local-time"]', formatTime(time.local_time, city.timezone));
        setText('[data-city-field="home-offset"]', time.offset_hours !== undefined && time.offset_hours !== null ? `${time.offset_hours}h from home` : 'Set home timezone');
        setText('[data-city-field="currency"]', intel.currency_code || city.currency_code || '—');
        setText('[data-city-field="currency-rate"]', intel.currency_rate ? `${intel.currency_rate} vs base` : '—');
        setText('[data-city-field="electrical"]', electrical?.plug_types || intel.electrical_plugs || '—');
        setText('[data-city-field="voltage"]', electrical?.voltage || intel.voltage || '—');
        setText('[data-city-field="visa"]', intel.visa || 'Check official guidance');

        setText('[data-city-field="budget-low"]', budget.low || '—');
        setText('[data-city-field="budget-mid"]', budget.mid || '—');
        setText('[data-city-field="budget-high"]', budget.high || '—');

        renderList('[data-city-list="neighborhoods"]', intel.neighborhoods, (item) => {
            const title = item.name || 'Area';
            const note = item.note ? `<span class="text-slate-500 dark:text-slate-400">${item.note}</span>` : '';
            return `<span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span><div><p class="font-semibold">${title}</p>${note}</div>`;
        });

        renderList('[data-city-list="checklist"]', intel.checklist, (item) => `<span class="mt-1 h-2 w-2 rounded-full bg-emerald-400"></span><span>${item}</span>`);
        renderList('[data-city-list="cultural"]', intel.cultural_notes, (item) => `<span class="mt-1 h-2 w-2 rounded-full bg-amber-400"></span><span>${item}</span>`);
        renderList('[data-city-list="emergency"]', emergencies, (item) => `<span class="mt-1 h-2 w-2 rounded-full bg-rose-400"></span><span class="font-semibold">${item.label || item.service}</span><span class="text-slate-500 dark:text-slate-400">${item.number}</span>`);
        renderList('[data-city-list="best-months"]', intel.best_months, (item) => `<span class="mt-1 h-2 w-2 rounded-full bg-sky-400"></span><span>${item}</span>`);
        renderList('[data-city-list="transport"]', intel.transport ? Object.values(intel.transport) : [], (item) => `<span class="mt-1 h-2 w-2 rounded-full bg-fuchsia-400"></span><span>${item}</span>`);
        renderList('[data-city-list="weather"]', intel.weather ? Object.values(intel.weather) : [], (item) => `<span class="mt-1 h-2 w-2 rounded-full bg-cyan-400"></span><span>${item}</span>`);
    };

    const focusMap = (city) => {
        if (!mapInstance || !city?.latitude || !city?.longitude) return;
        mapInstance.flyTo([city.latitude, city.longitude], 5, { duration: 0.8 });
        const cityMarker = L.circleMarker([city.latitude, city.longitude], {
            radius: 10,
            color: '#6366f1',
            fillColor: '#a5b4fc',
            fillOpacity: 0.7,
            weight: 2,
        });
        cityMarker.addTo(mapInstance);
        setTimeout(() => mapInstance.removeLayer(cityMarker), 1800);
    };

    const buildMap = (city = null) => {
        if (!mapContainer) return;

        mapInstance = L.map(mapContainer, {
            worldCopyJump: true,
            zoomControl: true,
            attributionControl: false,
        }).setView([20, 0], 2.2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 8,
        }).addTo(mapInstance);

        markerLayer = L.layerGroup().addTo(mapInstance);
        markers = (featuredCities || [])
            .filter((c) => c.latitude && c.longitude)
            .map((c) => {
                const marker = L.circleMarker([c.latitude, c.longitude], {
                    radius: 5,
                    color: '#38bdf8',
                    fillColor: '#22d3ee',
                    fillOpacity: 0.7,
                    weight: 1,
                }).addTo(markerLayer);
                marker.bindTooltip(c.name, { permanent: false });
                marker.on('click', () => fetchIntel(c.slug));
                return marker;
            });

        if (city?.latitude && city?.longitude) {
            mapInstance.setView([city.latitude, city.longitude], 4);
        }
    };

    const selectSuggestion = (slug, name) => {
        if (searchInput) {
            searchInput.value = name;
        }
        suggestions?.classList.add('hidden');
        fetchIntel(slug);
    };

    const renderSuggestions = (results = []) => {
        if (!suggestions) return;
        suggestions.innerHTML = '';
        if (!results.length) {
            suggestions.classList.add('hidden');
            return;
        }
        suggestions.classList.remove('hidden');
        results.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm hover:bg-white/70 dark:hover:bg-slate-800/80';
            button.innerHTML = `<div><p class="font-semibold">${item.name}</p><p class="text-xs text-slate-500">${item.state_region ?? ''} · ${item.country_code}</p></div><span class="text-[11px] text-indigo-600">${item.timezone ?? ''}</span>`;
            button.addEventListener('click', () => selectSuggestion(item.slug, item.name));
            suggestions.appendChild(button);
        });
    };

    let debounceTimer = null;
    const debouncedSearch = (query) => {
        if (!searchEndpoint) return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(async () => {
            if (!query) {
                renderSuggestions([]);
            return;
        }
        try {
            const { data } = await axios.get(searchEndpoint, { params: { q: query } });
            renderSuggestions(data.data || data);
        } catch (e) {
            console.error('City search failed', e);
        }
        }, 180);
    };

    const fetchIntel = async (slug) => {
        if (!slug) return;
        setLoading(true);
        try {
            const { data } = await axios.get(intelUrl(slug));
            updatePanel(data);
            focusMap(data.city);
        } catch (e) {
            console.error('Unable to load city intel', e);
        } finally {
            setLoading(false);
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', (event) => {
            debouncedSearch(event.target.value);
        });
    }

    if (initialPayload?.city) {
        updatePanel(initialPayload);
        buildMap(initialPayload.city);
    } else {
        buildMap();
    }
}
