/**
 * peta-layers.js
 *
 * Tanggung jawab:
 *   1. Inisialisasi FeatureGroup (L.featureGroup) untuk setiap item layer di sidebar
 *   2. Mendengarkan perubahan checkbox layer di sidebar
 *   3. Mendengarkan perubahan filter wilayah (kabupaten/kecamatan/kelurahan)
 *   4. Mendengarkan event moveend/zoomend Leaflet (bbox berubah)
 *   5. Fetch GET /api/v1/peta/bidang dengan params yang sesuai
 *   6. Render GeoJSON FeatureCollection ke Leaflet, warna per kategori/penggunaan/dll
 *   7. Cleanup polygon lama sebelum render ulang
 *
 * Layer types yang didukung (5 jenis):
 *   - kategori            → groups,                .layer-kategori-cb,            data-kategori-id,            cb-group-kategori
 *   - penggunaan          → penggunaanGroups,       .layer-penggunaan-cb,          data-penggunaan-id,          cb-group-penggunaan
 *   - jenis-hak           → jenisHakGroups,         .layer-jenis-hak-cb,           data-jenis-hak-id,           cb-group-jenis-hak
 *   - jenis-hak-adat      → jenisHakAdatGroups,     .layer-jenis-hak-adat-cb,      data-jenis-hak-adat-id,      cb-group-jenis-hak-adat
 *   - status-kesesuaian   → statusKesesuaianGroups, .layer-status-kesesuaian-cb,   data-status-kesesuaian-id,   cb-group-status-kesesuaian
 *
 * Dependensi global (dari map.js):
 *   window.petaMap  — instance L.Map
 */

"use strict";

// ── State ────────────────────────────────────────────────────────────────────

const petaLayers = {
    // FeatureGroup per kategori_id       → { [id]: { group: L.FeatureGroup, opacity: 1 } }
    groups: {},

    // FeatureGroup per penggunaan_id
    penggunaanGroups: {},

    // FeatureGroup per jenis_hak_id
    jenisHakGroups: {},

    // FeatureGroup per jenis_hak_adat_id
    jenisHakAdatGroups: {},

    // FeatureGroup per status_kesesuaian_id
    statusKesesuaianGroups: {},

    // Filter wilayah aktif
    wilayah: {
        kabupaten_kode: null,
        kecamatan_kode: null,
        kelurahan_kode: null,
    },

    // Debounce timer untuk moveend
    _fetchTimer: null,

    // Flag: sedang proses ganti filter wilayah — block moveend sementara
    _wilayahChanging: false,
};

// ── Inisialisasi FeatureGroup dari DOM ────────────────────────────────────────
//
// Setiap checkbox layer di sidebar memiliki data-*-id yang berisi ID record.
// Kita daftarkan L.featureGroup() untuk setiap ID agar polygon dapat ditampung
// sebelum ada fetch. Fungsi ini dipanggil sekali saat DOM siap + petaMap tersedia.

function initLayerGroups() {
    const map = window.petaMap;
    if (!map) return;

    // Kategori
    document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id || petaLayers.groups[id]) return;
        petaLayers.groups[id] = {
            group: L.featureGroup().addTo(map),
            opacity: 1,
        };
    });

    // Penggunaan
    document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.penggunaanId);
        if (!id || petaLayers.penggunaanGroups[id]) return;
        petaLayers.penggunaanGroups[id] = {
            group: L.featureGroup().addTo(map),
            opacity: 1,
        };
    });

    // Jenis Hak
    document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakId);
        if (!id || petaLayers.jenisHakGroups[id]) return;
        petaLayers.jenisHakGroups[id] = {
            group: L.featureGroup().addTo(map),
            opacity: 1,
        };
    });

    // Jenis Hak Adat
    document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakAdatId);
        if (!id || petaLayers.jenisHakAdatGroups[id]) return;
        petaLayers.jenisHakAdatGroups[id] = {
            group: L.featureGroup().addTo(map),
            opacity: 1,
        };
    });

    // Status Kesesuaian
    document.querySelectorAll(".layer-status-kesesuaian-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.statusKesesuaianId);
        if (!id || petaLayers.statusKesesuaianGroups[id]) return;
        petaLayers.statusKesesuaianGroups[id] = {
            group: L.featureGroup().addTo(map),
            opacity: 1,
        };
    });
}

// ── Init ─────────────────────────────────────────────────────────────────────

function initPetaLayers() {
    // Inisialisasi semua FeatureGroup sekarang bahwa petaMap sudah ada
    initLayerGroups();

    // Fetch ulang saat peta digeser/zoom — debounce 600ms
    window.petaMap.on("moveend zoomend", () => {
        // Skip jika sedang proses ganti filter wilayah (fitBounds dari loadWilayahLayer/fitBoundsToParent)
        if (petaLayers._wilayahChanging) return;

        clearTimeout(petaLayers._fetchTimer);
        petaLayers._fetchTimer = setTimeout(() => {
            const aktifKategori = getKategoriAktif();
            const aktifPenggunaan = getPenggunaanAktif();
            const aktifJenisHak = getJenisHakAktif();
            const aktifJenisHakAdat = getJenisHakAdatAktif();
            const aktifStatusKesesuaian = getStatusKesesuaianAktif();

            if (aktifKategori.length > 0) fetchDanRenderBidang(aktifKategori);
            if (aktifPenggunaan.length > 0)
                fetchDanRenderBidangPenggunaan(aktifPenggunaan);
            if (aktifJenisHak.length > 0)
                fetchDanRenderBidangJenisHak(aktifJenisHak);
            if (aktifJenisHakAdat.length > 0)
                fetchDanRenderBidangJenisHakAdat(aktifJenisHakAdat);
            if (aktifStatusKesesuaian.length > 0)
                fetchDanRenderBidangStatusKesesuaian(aktifStatusKesesuaian);
        }, 600);
    });
}

// ── Helpers: getter checkbox aktif ───────────────────────────────────────────

function getKategoriAktif() {
    return [...document.querySelectorAll(".layer-kategori-cb:checked")]
        .map((cb) => parseInt(cb.dataset.kategoriId))
        .filter(Boolean);
}

function getPenggunaanAktif() {
    return [...document.querySelectorAll(".layer-penggunaan-cb:checked")]
        .map((cb) => parseInt(cb.dataset.penggunaanId))
        .filter(Boolean);
}

function getJenisHakAktif() {
    return [...document.querySelectorAll(".layer-jenis-hak-cb:checked")]
        .map((cb) => parseInt(cb.dataset.jenisHakId))
        .filter(Boolean);
}

function getJenisHakAdatAktif() {
    return [...document.querySelectorAll(".layer-jenis-hak-adat-cb:checked")]
        .map((cb) => parseInt(cb.dataset.jenisHakAdatId))
        .filter(Boolean);
}

function getStatusKesesuaianAktif() {
    return [...document.querySelectorAll(".layer-status-kesesuaian-cb:checked")]
        .map((cb) => parseInt(cb.dataset.statusKesesuaianId))
        .filter(Boolean);
}

// ── Helpers: UI ───────────────────────────────────────────────────────────────

function getBboxString() {
    if (!window.petaMap) return null;
    const b = window.petaMap.getBounds();
    return [
        b.getWest().toFixed(6),
        b.getSouth().toFixed(6),
        b.getEast().toFixed(6),
        b.getNorth().toFixed(6),
    ].join(",");
}

function buildWilayahParams(params) {
    const { kabupaten_kode, kecamatan_kode, kelurahan_kode } =
        petaLayers.wilayah;
    if (kelurahan_kode) {
        params.set("kelurahan_kode", kelurahan_kode);
    } else if (kecamatan_kode) {
        params.set("kecamatan_kode", kecamatan_kode);
    } else if (kabupaten_kode) {
        params.set("kabupaten_kode", kabupaten_kode);
    } else {
        const bbox = getBboxString();
        if (bbox) params.set("bbox", bbox);
    }
}

function setLayerCountLabel(kategoriId, count) {
    const groupEl = document.querySelector(
        `[data-group-id="kategori-${kategoriId}"]`,
    );
    if (!groupEl) return;
    const label = groupEl.querySelector(".bidang-count");
    if (label)
        label.textContent = `${count.toLocaleString("id-ID")} bidang dimuat`;
}

function setLoadingState(kategoriIds, loading) {
    kategoriIds.forEach((id) => {
        const groupEl = document.querySelector(
            `[data-group-id="kategori-${id}"]`,
        );
        if (!groupEl) return;
        const label = groupEl.querySelector(".bidang-count");
        if (label && loading) label.textContent = "Memuat…";
    });
}

function updateGroupCheckboxState(groupCbId, cbSelector) {
    const groupCb = document.getElementById(groupCbId);
    if (!groupCb) return;
    const total = document.querySelectorAll(cbSelector).length;
    const checked = document.querySelectorAll(`${cbSelector}:checked`).length;
    if (checked === 0) {
        groupCb.checked = false;
        groupCb.indeterminate = false;
    } else if (checked === total) {
        groupCb.checked = true;
        groupCb.indeterminate = false;
    } else {
        groupCb.checked = false;
        groupCb.indeterminate = true;
    }
}

// ── Fetch & Render: Kategori ──────────────────────────────────────────────────

async function fetchDanRenderBidang(kategoriIds) {
    if (!kategoriIds.length) return;

    kategoriIds.forEach((id) => petaLayers.groups[id]?.group.clearLayers());
    setLoadingState(kategoriIds, true);

    const params = new URLSearchParams();
    kategoriIds.forEach((id) => params.append("kategori_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();
        const counts = {};
        kategoriIds.forEach((id) => {
            counts[id] = 0;
        });

        geojson.features?.forEach((feature) => {
            const { kategori_id, warna, no_bidang, no_persil, luas } =
                feature.properties;
            const reg = petaLayers.groups[kategori_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color: warna ?? "#3b82f6",
                    fillColor: warna ?? "#3b82f6",
                    fillOpacity: 0.25 * reg.opacity,
                    weight: 1.5,
                    opacity: reg.opacity,
                },
            });

            polygon.bindPopup(buildPopup(no_bidang, no_persil, luas));
            reg.group.addLayer(polygon);
            counts[kategori_id] = (counts[kategori_id] ?? 0) + 1;
        });

        kategoriIds.forEach((id) => setLayerCountLabel(id, counts[id] ?? 0));
    } catch (err) {
        console.error("[peta-layers] fetchDanRenderBidang error:", err);
        kategoriIds.forEach((id) => {
            const label = document.querySelector(
                `[data-group-id="kategori-${id}"] .bidang-count`,
            );
            if (label) label.textContent = "Gagal memuat data.";
        });
    }
}

// ── Fetch & Render: Penggunaan ────────────────────────────────────────────────

async function fetchDanRenderBidangPenggunaan(penggunaanIds) {
    if (!penggunaanIds.length) return;

    penggunaanIds.forEach((id) =>
        petaLayers.penggunaanGroups[id]?.group.clearLayers(),
    );

    const params = new URLSearchParams();
    penggunaanIds.forEach((id) => params.append("penggunaan_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();

        geojson.features?.forEach((feature) => {
            const {
                penggunaan_id,
                penggunaan_warna,
                no_bidang,
                no_persil,
                luas,
            } = feature.properties;
            const reg = petaLayers.penggunaanGroups[penggunaan_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color: penggunaan_warna ?? "#10b981",
                    fillColor: penggunaan_warna ?? "#10b981",
                    fillOpacity: 0.25 * reg.opacity,
                    weight: 1.5,
                    opacity: reg.opacity,
                },
            });

            polygon.bindPopup(buildPopup(no_bidang, no_persil, luas));
            reg.group.addLayer(polygon);
        });
    } catch (err) {
        console.error(
            "[peta-layers] fetchDanRenderBidangPenggunaan error:",
            err,
        );
    }
}

// ── Fetch & Render: Jenis Hak ─────────────────────────────────────────────────

async function fetchDanRenderBidangJenisHak(jenisHakIds) {
    if (!jenisHakIds.length) return;

    jenisHakIds.forEach((id) =>
        petaLayers.jenisHakGroups[id]?.group.clearLayers(),
    );

    const params = new URLSearchParams();
    jenisHakIds.forEach((id) => params.append("jenis_hak_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();

        geojson.features?.forEach((feature) => {
            const {
                jenis_hak_id,
                jenis_hak_warna,
                no_bidang,
                no_persil,
                luas,
            } = feature.properties;
            const reg = petaLayers.jenisHakGroups[jenis_hak_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color: jenis_hak_warna ?? "#f59e0b",
                    fillColor: jenis_hak_warna ?? "#f59e0b",
                    fillOpacity: 0.25 * reg.opacity,
                    weight: 1.5,
                    opacity: reg.opacity,
                },
            });

            polygon.bindPopup(buildPopup(no_bidang, no_persil, luas));
            reg.group.addLayer(polygon);
        });
    } catch (err) {
        console.error("[peta-layers] fetchDanRenderBidangJenisHak error:", err);
    }
}

// ── Fetch & Render: Jenis Hak Adat ───────────────────────────────────────────

async function fetchDanRenderBidangJenisHakAdat(jenisHakAdatIds) {
    if (!jenisHakAdatIds.length) return;

    jenisHakAdatIds.forEach((id) =>
        petaLayers.jenisHakAdatGroups[id]?.group.clearLayers(),
    );

    const params = new URLSearchParams();
    jenisHakAdatIds.forEach((id) => params.append("jenis_hak_adat_ids[]", id));
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();

        geojson.features?.forEach((feature) => {
            const {
                jenis_hak_adat_id,
                jenis_hak_adat_warna,
                no_bidang,
                no_persil,
                luas,
            } = feature.properties;
            const reg = petaLayers.jenisHakAdatGroups[jenis_hak_adat_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color: jenis_hak_adat_warna ?? "#8b5cf6",
                    fillColor: jenis_hak_adat_warna ?? "#8b5cf6",
                    fillOpacity: 0.25 * reg.opacity,
                    weight: 1.5,
                    opacity: reg.opacity,
                },
            });

            polygon.bindPopup(buildPopup(no_bidang, no_persil, luas));
            reg.group.addLayer(polygon);
        });
    } catch (err) {
        console.error(
            "[peta-layers] fetchDanRenderBidangJenisHakAdat error:",
            err,
        );
    }
}

// ── Fetch & Render: Status Kesesuaian ────────────────────────────────────────

async function fetchDanRenderBidangStatusKesesuaian(statusKesesuaianIds) {
    if (!statusKesesuaianIds.length) return;

    statusKesesuaianIds.forEach((id) =>
        petaLayers.statusKesesuaianGroups[id]?.group.clearLayers(),
    );

    const params = new URLSearchParams();
    statusKesesuaianIds.forEach((id) =>
        params.append("status_kesesuaian_ids[]", id),
    );
    buildWilayahParams(params);

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();

        geojson.features?.forEach((feature) => {
            const {
                status_kesesuaian_id,
                status_kesesuaian_warna,
                no_bidang,
                no_persil,
                luas,
            } = feature.properties;
            const reg = petaLayers.statusKesesuaianGroups[status_kesesuaian_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color: status_kesesuaian_warna ?? "#ef4444",
                    fillColor: status_kesesuaian_warna ?? "#ef4444",
                    fillOpacity: 0.25 * reg.opacity,
                    weight: 1.5,
                    opacity: reg.opacity,
                },
            });

            polygon.bindPopup(buildPopup(no_bidang, no_persil, luas));
            reg.group.addLayer(polygon);
        });
    } catch (err) {
        console.error(
            "[peta-layers] fetchDanRenderBidangStatusKesesuaian error:",
            err,
        );
    }
}

// ── Popup builder ─────────────────────────────────────────────────────────────

function buildPopup(no_bidang, no_persil, luas) {
    return `
        <div style="min-width:160px; font-size:12px; line-height:1.6;">
            <p style="font-weight:600; margin:0 0 4px;">${escHtml(no_bidang ?? "-")}</p>
            <p style="color:#6b7280; margin:0;">Persil: ${escHtml(no_persil ?? "-")}</p>
            ${luas ? `<p style="color:#6b7280; margin:0;">Luas: ${Number(luas).toLocaleString("id-ID")} m²</p>` : ""}
        </div>
    `;
}

// ── Event handlers ────────────────────────────────────────────────────────────

function onGroupChange(groupCb, groupName) {
    const isChecked = groupCb.checked;

    if (groupName === "kategori") {
        document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onKategoriChange();
    } else if (groupName === "penggunaan") {
        document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onPenggunaanChange();
    } else if (groupName === "jenis-hak") {
        document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onJenisHakChange();
    } else if (groupName === "jenis-hak-adat") {
        document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
            cb.checked = isChecked;
        });
        onJenisHakAdatChange();
    } else if (groupName === "status-kesesuaian") {
        document
            .querySelectorAll(".layer-status-kesesuaian-cb")
            .forEach((cb) => {
                cb.checked = isChecked;
            });
        onStatusKesesuaianChange();
    }
}

function onKategoriChange() {
    const aktif = getKategoriAktif();

    document.querySelectorAll(".layer-kategori-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id) return;
        if (!cb.checked) petaLayers.groups[id]?.group.clearLayers();
    });

    updateGroupCheckboxState("cb-group-kategori", ".layer-kategori-cb");

    if (aktif.length > 0) fetchDanRenderBidang(aktif);
}

function onPenggunaanChange() {
    const aktif = getPenggunaanAktif();

    document.querySelectorAll(".layer-penggunaan-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.penggunaanId);
        if (!id) return;
        if (!cb.checked) petaLayers.penggunaanGroups[id]?.group.clearLayers();
    });

    updateGroupCheckboxState("cb-group-penggunaan", ".layer-penggunaan-cb");

    if (aktif.length > 0) fetchDanRenderBidangPenggunaan(aktif);
}

function onJenisHakChange() {
    const aktif = getJenisHakAktif();

    document.querySelectorAll(".layer-jenis-hak-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakId);
        if (!id) return;
        if (!cb.checked) petaLayers.jenisHakGroups[id]?.group.clearLayers();
    });

    updateGroupCheckboxState("cb-group-jenis-hak", ".layer-jenis-hak-cb");

    if (aktif.length > 0) fetchDanRenderBidangJenisHak(aktif);
}

function onJenisHakAdatChange() {
    const aktif = getJenisHakAdatAktif();

    document.querySelectorAll(".layer-jenis-hak-adat-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.jenisHakAdatId);
        if (!id) return;
        if (!cb.checked) petaLayers.jenisHakAdatGroups[id]?.group.clearLayers();
    });

    updateGroupCheckboxState(
        "cb-group-jenis-hak-adat",
        ".layer-jenis-hak-adat-cb",
    );

    if (aktif.length > 0) fetchDanRenderBidangJenisHakAdat(aktif);
}

function onStatusKesesuaianChange() {
    const aktif = getStatusKesesuaianAktif();

    document.querySelectorAll(".layer-status-kesesuaian-cb").forEach((cb) => {
        const id = parseInt(cb.dataset.statusKesesuaianId);
        if (!id) return;
        if (!cb.checked)
            petaLayers.statusKesesuaianGroups[id]?.group.clearLayers();
    });

    updateGroupCheckboxState(
        "cb-group-status-kesesuaian",
        ".layer-status-kesesuaian-cb",
    );

    if (aktif.length > 0) fetchDanRenderBidangStatusKesesuaian(aktif);
}

// ── Opacity ───────────────────────────────────────────────────────────────────

function updateOpacity(rangeEl, groupId) {
    const val = rangeEl.value / 100;

    const opacityLabel = rangeEl
        .closest(".flex.items-center.gap-2")
        ?.querySelector(".opacity-val");
    if (opacityLabel) opacityLabel.textContent = `${rangeEl.value}%`;

    const applyOpacity = (registryMap, ids) => {
        ids.forEach((id) => {
            const r = registryMap[id];
            if (!r) return;
            r.opacity = val;
            r.group.eachLayer((layer) => {
                if (layer.setStyle)
                    layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
            });
        });
    };

    if (groupId === "group-kategori") {
        applyOpacity(petaLayers.groups, getKategoriAktif());
        return;
    }
    if (groupId === "group-penggunaan") {
        applyOpacity(petaLayers.penggunaanGroups, getPenggunaanAktif());
        return;
    }
    if (groupId === "group-jenis-hak") {
        applyOpacity(petaLayers.jenisHakGroups, getJenisHakAktif());
        return;
    }
    if (groupId === "group-jenis-hak-adat") {
        applyOpacity(petaLayers.jenisHakAdatGroups, getJenisHakAdatAktif());
        return;
    }
    if (groupId === "group-status-kesesuaian") {
        applyOpacity(
            petaLayers.statusKesesuaianGroups,
            getStatusKesesuaianAktif(),
        );
        return;
    }

    // Fallback: individual kategori layer
    const id = parseInt(groupId.replace("kategori-", ""));
    const reg = petaLayers.groups[id];
    if (!reg) return;
    reg.opacity = val;
    reg.group.eachLayer((layer) => {
        if (layer.setStyle)
            layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
    });
}

// ── Toggle group chevron ──────────────────────────────────────────────────────

function toggleGroup(headerEl) {
    const groupEl = headerEl.closest(".layer-group");
    const children = groupEl?.querySelector(".layer-children");
    const chevron = headerEl.querySelector(".chevron");

    if (!children) return;

    const isOpen = !children.classList.contains("hidden");
    children.classList.toggle("hidden", isOpen);
    chevron?.classList.toggle("rotate-90", !isOpen);
}

// ── Escape helpers ────────────────────────────────────────────────────────────

function escHtml(str) {
    return String(str ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function escAttr(str) {
    return String(str ?? "").replace(/"/g, "&quot;");
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────

// Tunggu map.js broadcast event petaMapReady sebelum inisialisasi layer
// Ini menghilangkan race condition antara map.js dan peta-layers.js
if (window.petaMap) {
    // Jika sudah ada (edge case urutan load berbeda), langsung init
    initPetaLayers();
} else {
    window.addEventListener('petaMapReady', () => initPetaLayers(), { once: true });
}
