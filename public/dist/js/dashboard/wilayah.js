/* ════════════════════════════════════════
   wilayah.js  (MapLibre edition)
   Depends on: window.petaMap (dari map.js, event petaMapReady)
   Exposes:    petaLayers.wilayah (baca oleh peta-layers.js)
════════════════════════════════════════ */

"use strict";

const PROVINSI_DEFAULT = 34;

const API = {
    kabupaten: "/api/v1/wilayah/kabupaten",
    kecamatan: "/api/v1/wilayah/kecamatan",
    kelurahan: "/api/v1/wilayah/kelurahan",
};

const WILAYAH_HIERARCHY = ["kabupaten", "kecamatan", "kelurahan"];

const LEVEL_CONFIG = {
    kabupaten: {
        label: "Semua Kabupaten/Kota",
        selector: "#filter-kabupaten",
        wrapper: "#wrapper-kabupaten",
        bbox: "/api/v1/wilayah/kabupaten/bbox",
        paintFill:   { "fill-color": "#2563eb", "fill-opacity": 0.06 },
        paintLine:   { "line-color": "#2563eb", "line-width": 2.5 },
        children: ["kecamatan", "kelurahan"],
    },
    kecamatan: {
        label: "Semua Kapanewon/Kemantren",
        selector: "#filter-kecamatan",
        wrapper: "#wrapper-kecamatan",
        bbox: "/api/v1/wilayah/kecamatan/bbox",
        paintFill:   { "fill-color": "#d97706", "fill-opacity": 0.08 },
        paintLine:   { "line-color": "#d97706", "line-width": 2 },
        children: ["kelurahan"],
    },
    kelurahan: {
        label: "Semua Kalurahan/Kelurahan",
        selector: "#filter-kelurahan",
        wrapper: "#wrapper-kelurahan",
        bbox: "/api/v1/wilayah/kelurahan/bbox",
        paintFill:   { "fill-color": "#059669", "fill-opacity": 0.1 },
        paintLine:   { "line-color": "#059669", "line-width": 1.5 },
        children: [],
    },
};

// ── State wilayah (dibaca oleh peta-layers.js) ────────────────────────────────
// petaLayers.wilayah sudah diinisialisasi di peta-layers.js;
// wilayah.js hanya mengisi nilainya.

/* ── Tambahkan source & layer wilayah ke map ────────────────────────────────── */
function addWilayahLayers(map) {
    // Source GeoJSON untuk batas wilayah yang dipilih (bbox highlight)
    if (!map.getSource("wilayah-highlight")) {
        map.addSource("wilayah-highlight", {
            type: "geojson",
            data: { type: "FeatureCollection", features: [] },
        });
    }

    // Layer fill
    if (!map.getLayer("wilayah-highlight-fill")) {
        map.addLayer({
            id: "wilayah-highlight-fill",
            type: "fill",
            source: "wilayah-highlight",
            paint: {
                "fill-color": "#2563eb",
                "fill-opacity": 0.06,
            },
        });
    }

    // Layer outline
    if (!map.getLayer("wilayah-highlight-line")) {
        map.addLayer({
            id: "wilayah-highlight-line",
            type: "line",
            source: "wilayah-highlight",
            paint: {
                "line-color": "#2563eb",
                "line-width": 2.5,
            },
        });
    }
}

/* ── Load dropdown kabupaten saat halaman dimuat ─────────────────────────────── */
async function loadKabupaten() {
    try {
        const res  = await fetch(`${API.kabupaten}?provinsi_id=${PROVINSI_DEFAULT}`);
        const data = await res.json();
        const sel  = document.querySelector(LEVEL_CONFIG.kabupaten.selector);
        if (!sel) return;

        sel.innerHTML = `<option value="">Semua Kabupaten/Kota</option>`;
        (data.data ?? data).forEach((kab) => {
            sel.insertAdjacentHTML(
                "beforeend",
                `<option value="${kab.kode}">${kab.nama}</option>`
            );
        });

        sel.addEventListener("change", () => onWilayahChange("kabupaten", sel.value));
    } catch (e) {
        console.warn("[wilayah] loadKabupaten error:", e);
    }
}

async function loadChildren(parentLevel, parentKode) {
    const cfg = LEVEL_CONFIG[parentLevel];
    for (const childLevel of cfg.children) {
        const childCfg = LEVEL_CONFIG[childLevel];
        const wrapper  = document.querySelector(childCfg.wrapper);
        if (!wrapper) continue;

        if (!parentKode) {
            wrapper.classList.add("hidden");
            document.querySelector(childCfg.selector).innerHTML =
                `<option value="">${childCfg.label}</option>`;
            continue;
        }

        wrapper.classList.remove("hidden");

        try {
            const paramKey = `${parentLevel}_kode`;
            const res      = await fetch(`${API[childLevel]}?${paramKey}=${parentKode}`);
            const data     = await res.json();
            const sel      = document.querySelector(childCfg.selector);
            if (!sel) return;

            sel.innerHTML = `<option value="">${childCfg.label}</option>`;
            (data.data ?? data).forEach((item) => {
                sel.insertAdjacentHTML(
                    "beforeend",
                    `<option value="${item.kode}">${item.nama}</option>`
                );
            });

            sel.addEventListener("change", () =>
                onWilayahChange(childLevel, sel.value)
            );
        } catch (e) {
            console.warn(`[wilayah] loadChildren(${childLevel}) error:`, e);
        }
    }
}

async function onWilayahChange(level, kode) {
    const cfg = LEVEL_CONFIG[level];
    if (!cfg) return;

    // Reset children
    for (const child of cfg.children) {
        const childCfg = LEVEL_CONFIG[child];
        const wrapper  = document.querySelector(childCfg.wrapper);
        if (wrapper) wrapper.classList.add("hidden");
        const sel = document.querySelector(childCfg.selector);
        if (sel) sel.innerHTML = `<option value="">${childCfg.label}</option>`;
    }

    // Update state wilayah di petaLayers
    if (window.petaLayers?.wilayah) {
        window.petaLayers.wilayah.kabupaten_kode = null;
        window.petaLayers.wilayah.kecamatan_kode  = null;
        window.petaLayers.wilayah.kelurahan_kode  = null;

        if (kode) {
            window.petaLayers.wilayah[`${level}_kode`] = kode;
        }
    }

    if (!kode) {
        // Clear highlight
        const map = window.petaMap;
        if (map && map.getSource("wilayah-highlight")) {
            map.getSource("wilayah-highlight").setData({
                type: "FeatureCollection",
                features: [],
            });
        }

        // Panggil refetch layer bidang
        window.dispatchEvent(new CustomEvent("wilayahChanged"));
        return;
    }

    // Load children dropdowns
    await loadChildren(level, kode);

    // Fly to bbox wilayah
    try {
        const bboxRes  = await fetch(`${cfg.bbox}?kode=${kode}`);
        const bboxData = await bboxRes.json();

        if (bboxData?.bbox) {
            const [minLng, minLat, maxLng, maxLat] = bboxData.bbox;
            window.petaMap?.fitBounds(
                [[minLng, minLat], [maxLng, maxLat]],
                { padding: 40, maxZoom: 14 }
            );
        }

        // Update wilayah highlight dari GeoJSON endpoint
        const gjRes  = await fetch(`/api/v1/wilayah/${level}/geojson?kode=${kode}`);
        const gjData = await gjRes.json();

        const map = window.petaMap;
        if (map && map.getSource("wilayah-highlight")) {
            map.getSource("wilayah-highlight").setData(gjData);

            // Update style sesuai level
            map.setPaintProperty("wilayah-highlight-fill", "fill-color",    cfg.paintFill["fill-color"]);
            map.setPaintProperty("wilayah-highlight-fill", "fill-opacity",  cfg.paintFill["fill-opacity"]);
            map.setPaintProperty("wilayah-highlight-line", "line-color",    cfg.paintLine["line-color"]);
            map.setPaintProperty("wilayah-highlight-line", "line-width",    cfg.paintLine["line-width"]);
        }
    } catch (e) {
        console.warn("[wilayah] bbox/geojson fetch error:", e);
    }

    // Trigger refetch layer bidang dengan filter wilayah baru
    window.dispatchEvent(new CustomEvent("wilayahChanged"));
}

/* ── Bootstrap ─────────────────────────────────────────────────────────────── */
function initWilayah(map) {
    addWilayahLayers(map);
    loadKabupaten();
}

if (window.petaMap) {
    if (window.petaMap.isStyleLoaded()) {
        initWilayah(window.petaMap);
    } else {
        window.petaMap.once("load", () => initWilayah(window.petaMap));
    }
} else {
    window.addEventListener("petaMapReady", (e) => {
        e.detail.once("load", () => initWilayah(e.detail));
    }, { once: true });
}

// Re-init setelah basemap change
window.addEventListener("basemapChanged", () => {
    if (window.petaMap) {
        window.petaMap.once("styledata", () => addWilayahLayers(window.petaMap));
    }
});
