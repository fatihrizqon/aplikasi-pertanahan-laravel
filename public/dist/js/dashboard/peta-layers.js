/**
 * peta-layers.js
 *
 * Tanggung jawab:
 *   1. Mendengarkan perubahan checkbox KepemilikanTanah di sidebar
 *   2. Mendengarkan perubahan filter wilayah (kabupaten/kecamatan/kelurahan)
 *   3. Mendengarkan event moveend/zoomend Leaflet (bbox berubah)
 *   4. Fetch GET /api/v1/peta/bidang dengan params yang sesuai
 *   5. Render GeoJSON FeatureCollection ke Leaflet, warna per kategori
 *   6. Cleanup polygon lama sebelum render ulang
 *
 * Dependensi global (dari index.blade.php):
 *   window.petaMap       — instance L.Map (diset oleh map.js)
 *   window.kategoriData — array KepemilikanTanah dari blade
 */

'use strict';

// ── State ────────────────────────────────────────────────────────────────────

const petaLayers = {
    // FeatureGroup per kategori_id → { group: L.featureGroup, opacity: 1 }
    groups: {},

    // FeatureGroup per jenis_hak_id → { group: L.featureGroup, opacity: 1 }
    jenisHakGroups: {},

    // Filter wilayah aktif
    wilayah: {
        kabupaten_kode: null,
        kecamatan_kode: null,
        kelurahan_kode: null,
    },

    // Debounce timer untuk moveend
    _fetchTimer: null,
};

// ── Init: dipanggil setelah map.js selesai init petaMap ──────────────────────

function initPetaLayers() {
    if (!window.petaMap) {
        // Tunggu map siap
        setTimeout(initPetaLayers, 200);
        return;
    }

    // Inisialisasi FeatureGroup per kategori
    (kategoriData ?? []).forEach(k => {
        const group = L.featureGroup().addTo(window.petaMap);
        petaLayers.groups[k.id] = { group, opacity: 1 };
    });

    // Inisialisasi FeatureGroup per jenis hak
    (jenisHakData ?? []).forEach(j => {
        const group = L.featureGroup().addTo(window.petaMap);
        petaLayers.jenisHakGroups[j.id] = { group, opacity: 1 };
    });

    // Fetch ulang saat peta digeser/zoom — debounce 600ms
    window.petaMap.on('moveend zoomend', () => {
        clearTimeout(petaLayers._fetchTimer);
        petaLayers._fetchTimer = setTimeout(() => {
            const aktifKategori  = getKepemilikanAktif();
            const aktifJenisHak  = getJenisHakAktif();
            if (aktifKategori.length > 0)  fetchDanRenderBidang(aktifKategori);
            if (aktifJenisHak.length > 0)  fetchDanRenderBidangJenisHak(aktifJenisHak);
        }, 600);
    });
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function getKepemilikanAktif() {
    return [...document.querySelectorAll('.layer-kategori-cb:checked')]
        .map(cb => parseInt(cb.dataset.kategoriId))
        .filter(Boolean);
}

function getJenisHakAktif() {
    return [...document.querySelectorAll('.layer-jenis-hak-cb:checked')]
        .map(cb => parseInt(cb.dataset.jenisHakId))
        .filter(Boolean);
}

function getBboxString() {
    if (!window.petaMap) return null;
    const b = window.petaMap.getBounds();
    return [
        b.getWest().toFixed(6),
        b.getSouth().toFixed(6),
        b.getEast().toFixed(6),
        b.getNorth().toFixed(6),
    ].join(',');
}

function setLayerCountLabel(kategoriId, count) {
    const groupEl = document.querySelector(`[data-group-id="kategori-${kategoriId}"]`);
    if (!groupEl) return;
    const label = groupEl.querySelector('.bidang-count');
    if (label) label.textContent = `${count.toLocaleString('id-ID')} bidang dimuat`;
}

function setLoadingState(kategoriIds, loading) {
    kategoriIds.forEach(id => {
        const groupEl = document.querySelector(`[data-group-id="kategori-${id}"]`);
        if (!groupEl) return;
        const label = groupEl.querySelector('.bidang-count');
        if (label && loading) label.textContent = 'Memuat…';
    });
}

// ── Fetch & Render ───────────────────────────────────────────────────────────

async function fetchDanRenderBidang(kategoriIds) {
    if (!kategoriIds.length) return;

    // Bersihkan layer kategori yang aktif sebelum render ulang
    kategoriIds.forEach(id => {
        petaLayers.groups[id]?.group.clearLayers();
    });

    setLoadingState(kategoriIds, true);

    // Build query params
    const params = new URLSearchParams();
    kategoriIds.forEach(id => params.append('kategori_ids[]', id));

    const { kabupaten_kode, kecamatan_kode, kelurahan_kode } = petaLayers.wilayah;

    if (kelurahan_kode) {
        params.set('kelurahan_kode', kelurahan_kode);
    } else if (kecamatan_kode) {
        params.set('kecamatan_kode', kecamatan_kode);
    } else if (kabupaten_kode) {
        params.set('kabupaten_kode', kabupaten_kode);
    } else {
        // Tidak ada filter wilayah → pakai bbox viewport
        const bbox = getBboxString();
        if (bbox) params.set('bbox', bbox);
    }

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();

        // Hitung per kategori untuk label
        const counts = {};
        kategoriIds.forEach(id => { counts[id] = 0; });

        geojson.features?.forEach(feature => {
            const { kategori_id, warna, no_bidang, no_persil, luas } = feature.properties;

            const reg = petaLayers.groups[kategori_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color:       warna ?? '#3b82f6',
                    fillColor:   warna ?? '#3b82f6',
                    fillOpacity: 0.25 * reg.opacity,
                    weight:      1.5,
                    opacity:     reg.opacity,
                },
            });

            polygon.bindPopup(`
                <div style="min-width:160px; font-size:12px; line-height:1.6;">
                    <p style="font-weight:600; margin:0 0 4px;">${escHtml(no_bidang ?? '-')}</p>
                    <p style="color:#6b7280; margin:0;">Persil: ${escHtml(no_persil ?? '-')}</p>
                    ${luas ? `<p style="color:#6b7280; margin:0;">Luas: ${Number(luas).toLocaleString('id-ID')} m²</p>` : ''}
                </div>
            `);

            reg.group.addLayer(polygon);
            counts[kategori_id] = (counts[kategori_id] ?? 0) + 1;
        });

        // Update label count per kategori
        kategoriIds.forEach(id => setLayerCountLabel(id, counts[id] ?? 0));

    } catch (err) {
        console.error('[peta-layers] fetchDanRenderBidang error:', err);
        kategoriIds.forEach(id => {
            const groupEl = document.querySelector(`[data-group-id="kategori-${id}"]`);
            const label   = groupEl?.querySelector('.bidang-count');
            if (label) label.textContent = 'Gagal memuat data.';
        });
    }
}

// ── Fetch & Render Jenis Hak ─────────────────────────────────────────────────

async function fetchDanRenderBidangJenisHak(jenisHakIds) {
    if (!jenisHakIds.length) return;

    // Bersihkan layer jenis hak yang aktif
    jenisHakIds.forEach(id => {
        petaLayers.jenisHakGroups[id]?.group.clearLayers();
    });

    // Build query params
    const params = new URLSearchParams();
    jenisHakIds.forEach(id => params.append('jenis_hak_ids[]', id));

    const { kabupaten_kode, kecamatan_kode, kelurahan_kode } = petaLayers.wilayah;

    if (kelurahan_kode) {
        params.set('kelurahan_kode', kelurahan_kode);
    } else if (kecamatan_kode) {
        params.set('kecamatan_kode', kecamatan_kode);
    } else if (kabupaten_kode) {
        params.set('kabupaten_kode', kabupaten_kode);
    } else {
        const bbox = getBboxString();
        if (bbox) params.set('bbox', bbox);
    }

    try {
        const res = await fetch(`/api/v1/peta/bidang?${params.toString()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const geojson = await res.json();

        geojson.features?.forEach(feature => {
            const { jenis_hak_id, jenis_hak_warna, no_bidang, no_persil, luas } = feature.properties;

            const reg = petaLayers.jenisHakGroups[jenis_hak_id];
            if (!reg) return;

            const polygon = L.geoJSON(feature, {
                style: {
                    color:       jenis_hak_warna ?? '#f59e0b',
                    fillColor:   jenis_hak_warna ?? '#f59e0b',
                    fillOpacity: 0.25 * reg.opacity,
                    weight:      1.5,
                    opacity:     reg.opacity,
                },
            });

            polygon.bindPopup(`
                <div style="min-width:160px; font-size:12px; line-height:1.6;">
                    <p style="font-weight:600; margin:0 0 4px;">${escHtml(no_bidang ?? '-')}</p>
                    <p style="color:#6b7280; margin:0;">Persil: ${escHtml(no_persil ?? '-')}</p>
                    ${luas ? `<p style="color:#6b7280; margin:0;">Luas: ${Number(luas).toLocaleString('id-ID')} m²</p>` : ''}
                </div>
            `);

            reg.group.addLayer(polygon);
        });

    } catch (err) {
        console.error('[peta-layers] fetchDanRenderBidangJenisHak error:', err);
    }
}

// ── Event handlers (dipanggil dari blade) ────────────────────────────────────

// Propagate level-1 group checkbox ke semua level-2 checkboxes
function onGroupChange(groupCb, groupName) {
    const isChecked = groupCb.checked;

    if (groupName === 'kepemilikan') {
        document.querySelectorAll('.layer-kategori-cb').forEach(cb => {
            cb.checked = isChecked;
        });
        onKepemilikanChange();
    } else if (groupName === 'jenis_hak') {
        document.querySelectorAll('.layer-jenis-hak-cb').forEach(cb => {
            cb.checked = isChecked;
        });
        onJenisHakChange();
    }
}

function onKepemilikanChange() {
    const aktif = getKepemilikanAktif();

    // Bug fix #2: Gunakan data-kategori-id langsung dari checkbox,
    // bukan selector data-group-id yang tidak ada di HTML
    const allCbs = document.querySelectorAll('.layer-kategori-cb');
    allCbs.forEach(cb => {
        const id = parseInt(cb.dataset.kategoriId);
        if (!id) return;

        if (cb.checked) {
            // Tidak perlu aksi tambahan — fetchDanRenderBidang akan render
        } else {
            // Unchecked → bersihkan layer dari peta langsung via petaLayers.groups
            petaLayers.groups[id]?.group.clearLayers();
        }
    });

    // Update state checkbox level-1 (indeterminate / checked / unchecked)
    const groupCb = document.getElementById('cb-group-kepemilikan');
    if (groupCb) {
        const total   = document.querySelectorAll('.layer-kategori-cb').length;
        const checked = aktif.length;
        if (checked === 0) {
            groupCb.checked       = false;
            groupCb.indeterminate = false;
        } else if (checked === total) {
            groupCb.checked       = true;
            groupCb.indeterminate = false;
        } else {
            groupCb.checked       = false;
            groupCb.indeterminate = true;
        }
    }

    if (aktif.length > 0) fetchDanRenderBidang(aktif);
}

function onJenisHakChange() {
    const aktif = getJenisHakAktif();

    // Clear layer yang unchecked
    const allCbs = document.querySelectorAll('.layer-jenis-hak-cb');
    allCbs.forEach(cb => {
        const id = parseInt(cb.dataset.jenisHakId);
        if (!id) return;
        if (!cb.checked) {
            petaLayers.jenisHakGroups[id]?.group.clearLayers();
        }
    });

    // Update indeterminate state checkbox level-1
    const groupCb = document.getElementById('cb-group-jenis-hak');
    if (groupCb) {
        const total   = document.querySelectorAll('.layer-jenis-hak-cb').length;
        const checked = aktif.length;
        if (checked === 0) {
            groupCb.checked       = false;
            groupCb.indeterminate = false;
        } else if (checked === total) {
            groupCb.checked       = true;
            groupCb.indeterminate = false;
        } else {
            groupCb.checked       = false;
            groupCb.indeterminate = true;
        }
    }

    if (aktif.length > 0) fetchDanRenderBidangJenisHak(aktif);
}

function onKabupatenChange(kode) {
    petaLayers.wilayah.kabupaten_kode = kode || null;
    petaLayers.wilayah.kecamatan_kode = null;
    petaLayers.wilayah.kelurahan_kode = null;

    // Reset dropdown bawahnya
    const wrapKec = document.getElementById('wrapper-kecamatan');
    const wrapKel = document.getElementById('wrapper-kelurahan');
    const selKec  = document.getElementById('filter-kecamatan');
    const selKel  = document.getElementById('filter-kelurahan');

    selKec.innerHTML  = '<option value="">Semua Kecamatan</option>';
    selKel.innerHTML  = '<option value="">Semua Kelurahan/Desa</option>';
    wrapKel.classList.add('hidden');

    if (kode) {
        wrapKec.classList.remove('hidden');
        // Fetch kecamatan
        fetch(`/api/v1/wilayah/kecamatan?kode=${kode}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(item => {
                    selKec.insertAdjacentHTML('beforeend',
                        `<option value="${escAttr(item.value)}">${escHtml(item.label)}</option>`);
                });
            });
    } else {
        wrapKec.classList.add('hidden');
    }

    const aktif = getKepemilikanAktif();
    if (aktif.length > 0) fetchDanRenderBidang(aktif);
}

function onKecamatanChange(kode) {
    petaLayers.wilayah.kecamatan_kode = kode || null;
    petaLayers.wilayah.kelurahan_kode = null;

    const wrapKel = document.getElementById('wrapper-kelurahan');
    const selKel  = document.getElementById('filter-kelurahan');

    selKel.innerHTML = '<option value="">Semua Kelurahan/Desa</option>';

    if (kode) {
        wrapKel.classList.remove('hidden');
        fetch(`/api/v1/wilayah/kelurahan?kode=${kode}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(item => {
                    selKel.insertAdjacentHTML('beforeend',
                        `<option value="${escAttr(item.value)}">${escHtml(item.label)}</option>`);
                });
            });

        // Zoom ke kecamatan
        fetch(`/api/v1/wilayah/kecamatan/bbox?kode=${kode}`)
            .then(r => r.json())
            .then(({ bbox }) => {
                if (bbox && window.petaMap) {
                    window.petaMap.fitBounds([[bbox[1], bbox[0]], [bbox[3], bbox[2]]]);
                }
            });
    } else {
        wrapKel.classList.add('hidden');
        // Kembali ke bbox kabupaten jika ada
        if (petaLayers.wilayah.kabupaten_kode) {
            fetch(`/api/v1/wilayah/kabupaten/bbox?kode=${petaLayers.wilayah.kabupaten_kode}`)
                .then(r => r.json())
                .then(({ bbox }) => {
                    if (bbox && window.petaMap) {
                        window.petaMap.fitBounds([[bbox[1], bbox[0]], [bbox[3], bbox[2]]]);
                    }
                });
        }
    }

    const aktif = getKepemilikanAktif();
    if (aktif.length > 0) fetchDanRenderBidang(aktif);
}

function onKelurahanChange(kode) {
    petaLayers.wilayah.kelurahan_kode = kode || null;

    if (kode) {
        fetch(`/api/v1/wilayah/kelurahan/bbox?kode=${kode}`)
            .then(r => r.json())
            .then(({ bbox }) => {
                if (bbox && window.petaMap) {
                    window.petaMap.fitBounds([[bbox[1], bbox[0]], [bbox[3], bbox[2]]]);
                }
            });
    }

    const aktif = getKepemilikanAktif();
    if (aktif.length > 0) fetchDanRenderBidang(aktif);
}

// ── Opacity ──────────────────────────────────────────────────────────────────

function updateOpacity(rangeEl, groupId) {
    const val = rangeEl.value / 100;

    // Update label
    rangeEl.closest('.opacity-slider-row')
        ?.querySelector('.opacity-val')
        ?.setAttribute('textContent', `${rangeEl.value}%`);
    const opacityLabel = rangeEl.closest('.opacity-slider-row')?.querySelector('.opacity-val');
    if (opacityLabel) opacityLabel.textContent = `${rangeEl.value}%`;

    let reg = null;
    if (groupId === 'group-jenis-hak') {
        // Opacity untuk seluruh grup jenis hak yang aktif
        getJenisHakAktif().forEach(id => {
            const r = petaLayers.jenisHakGroups[id];
            if (!r) return;
            r.opacity = val;
            r.group.eachLayer(layer => {
                if (layer.setStyle) layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
            });
        });
        return;
    }

    // Ekstrak kategori_id dari groupId "kategori-{id}" atau "group-kepemilikan"
    if (groupId === 'group-kepemilikan') {
        getKepemilikanAktif().forEach(id => {
            const r = petaLayers.groups[id];
            if (!r) return;
            r.opacity = val;
            r.group.eachLayer(layer => {
                if (layer.setStyle) layer.setStyle({ fillOpacity: 0.25 * val, opacity: val });
            });
        });
        return;
    }

    const id = parseInt(groupId.replace('kategori-', ''));
    reg = petaLayers.groups[id];
    if (!reg) return;

    reg.opacity = val;

    reg.group.eachLayer(layer => {
        if (layer.setStyle) {
            layer.setStyle({
                fillOpacity: 0.25 * val,
                opacity:     val,
            });
        }
    });
}

// ── Toggle group chevron (existing pattern) ───────────────────────────────────

function toggleGroup(headerEl) {
    const groupEl  = headerEl.closest('.layer-group');
    const children = groupEl?.querySelector('.layer-children');
    const chevron  = headerEl.querySelector('.chevron');

    if (!children) return;

    const isOpen = !children.classList.contains('hidden');
    children.classList.toggle('hidden', isOpen);
    chevron?.classList.toggle('rotate-90', !isOpen);
}

// ── Escape helpers ────────────────────────────────────────────────────────────

function escHtml(str) {
    return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escAttr(str) {
    return String(str ?? '').replace(/"/g, '&quot;');
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', initPetaLayers);
