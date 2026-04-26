/* ════════════════════════════════════════
   layers.js  (MapLibre edition)
   Depends on: window.petaMap (dari map.js)
   Exposes:    window.layerRegistry, window.toggleLayer, window.updateOpacity, window.toggleGroup
   Catatan: file ini untuk layer statis (WMS/GeoJSON) selain bidang tanah.
            Layer bidang dikelola oleh peta-layers.js menggunakan vector tiles.
════════════════════════════════════════ */

"use strict";

/**
 * Registry layer statis (WMS tile atau GeoJSON).
 * Key harus sama dengan data-layer-key di blade/HTML.
 *
 * Contoh menambah layer WMS (uncomment dan sesuaikan):
 *
 * window.layerRegistry['rtrw-bantul'] = {
 *     type: 'raster',
 *     sourceId: 'rtrw-bantul-src',
 *     source: {
 *         type: 'raster',
 *         tiles: ['https://geoserver.example.com/wms?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetMap&LAYERS=bantul:rtrw&BBOX={bbox-epsg-3857}&WIDTH=256&HEIGHT=256&SRS=EPSG:3857&FORMAT=image/png&TRANSPARENT=true'],
 *         tileSize: 256,
 *     },
 *     layer: {
 *         id: 'rtrw-bantul',
 *         type: 'raster',
 *         source: 'rtrw-bantul-src',
 *         paint: { 'raster-opacity': 1 },
 *     },
 * };
 *
 * Contoh menambah layer GeoJSON:
 *
 * window.layerRegistry['batas-persil'] = {
 *     type: 'geojson',
 *     sourceId: 'batas-persil-src',
 *     source: { type: 'geojson', data: '/api/v1/geojson/batas' },
 *     layers: [
 *         { id: 'batas-persil-fill', type: 'fill', source: 'batas-persil-src', paint: { 'fill-color': '#374151', 'fill-opacity': 0.1 } },
 *         { id: 'batas-persil-line', type: 'line', source: 'batas-persil-src', paint: { 'line-color': '#374151', 'line-width': 1.5 } },
 *     ],
 * };
 */
window.layerRegistry = {};

/* ── Add layer registry to map ───────────────────────────────────────────────── */
function addRegistryLayersToMap(map) {
    Object.entries(window.layerRegistry).forEach(([key, def]) => {
        if (!def._added) return;

        // Re-add source & layers (dipanggil ulang setelah basemap change)
        if (!map.getSource(def.sourceId)) {
            map.addSource(def.sourceId, def.source);
        }

        const layers = def.layers ?? [def.layer];
        layers.forEach((layer) => {
            if (!map.getLayer(layer.id)) {
                map.addLayer(layer);
            }
        });
    });
}

/* ── Toggle layer on/off ─────────────────────────────────────────────────────── */
window.toggleLayer = function (checkbox, layerKey) {
    const map = window.petaMap;
    const def = window.layerRegistry[layerKey];

    if (!map || !def) return;

    if (checkbox.checked) {
        // Add source jika belum ada
        if (!map.getSource(def.sourceId)) {
            map.addSource(def.sourceId, def.source);
        }

        // Add layers
        const layers = def.layers ?? [def.layer];
        layers.forEach((layer) => {
            if (!map.getLayer(layer.id)) {
                map.addLayer(layer);
            } else {
                map.setLayoutProperty(layer.id, "visibility", "visible");
            }
        });

        def._added = true;
    } else {
        // Hide layers
        const layers = def.layers ?? [def.layer];
        layers.forEach((layer) => {
            if (map.getLayer(layer.id)) {
                map.setLayoutProperty(layer.id, "visibility", "none");
            }
        });
    }
};

// Alias untuk backward compatibility
window.toggleLeafLayer = window.toggleLayer;

/* ── Update opacity ──────────────────────────────────────────────────────────── */
window.updateOpacity = function (slider, layerKey) {
    const row = slider.closest(".opacity-slider-row");
    if (row) row.querySelector(".opacity-val").textContent = slider.value + "%";

    const map = window.petaMap;
    const def = window.layerRegistry[layerKey];
    if (!map || !def) return;

    const val    = slider.value / 100;
    const layers = def.layers ?? [def.layer];

    layers.forEach((layer) => {
        if (!map.getLayer(layer.id)) return;

        if (layer.type === "raster") {
            map.setPaintProperty(layer.id, "raster-opacity", val);
        } else if (layer.type === "fill") {
            map.setPaintProperty(layer.id, "fill-opacity", val * 0.5);
        } else if (layer.type === "line") {
            map.setPaintProperty(layer.id, "line-opacity", val);
        }
    });
};

/* ── Accordion toggle ────────────────────────────────────────────────────────── */
window.toggleGroup = function (headerEl) {
    const group    = headerEl.closest(".layer-group");
    const children = group?.querySelector(":scope > .layer-children");
    const chevron  = headerEl.querySelector(".chevron");
    if (!children) return;

    const isOpen = !children.classList.contains("hidden");
    children.classList.toggle("hidden", isOpen);
    if (chevron) chevron.style.transform = isOpen ? "" : "rotate(90deg)";
};

/* ── Re-attach layers setelah basemap change ────────────────────────────────── */
window.addEventListener("basemapChanged", () => {
    const map = window.petaMap;
    if (!map) return;
    map.once("styledata", () => addRegistryLayersToMap(map));
});
