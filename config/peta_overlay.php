<?php
/**
 * config/peta_overlay.php
 *
 * Konfigurasi semua overlay layer independen (bukan bidang-based).
 * Setiap entry = satu "sub-grup" di sidebar (level 2).
 *
 * Struktur:
 *   group_key       => key grup level-1 di sidebar (misal 'rtrw', 'rdtr')
 *   label           => label tampil di sidebar level-2
 *   geom_table      => tabel yang punya kolom geom
 *   geom_pk         => primary key tabel geom (default 'id', polaruang pakai 'gid')
 *   kawasan_table   => tabel kawasan (null = tidak ada sub-item, flat)
 *   kawasan_fk      => FK di geom_table yang menunjuk kawasan_table (null jika flat)
 */

return [

    // ── RTRW ────────────────────────────────────────────────────────────────
    'polaruang-bantul' => [
        'group_key'     => 'rtrw',
        'label'         => 'RTRW Kab. Bantul',
        'geom_table'    => 'polaruang_bantul',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'polaruang_bantul_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'polaruang-gk' => [
        'group_key'     => 'rtrw',
        'label'         => 'RTRW Kab. Gunungkidul',
        'geom_table'    => 'polaruang_gk',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'polaruang_gk_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'polaruang-kp' => [
        'group_key'     => 'rtrw',
        'label'         => 'RTRW Kab. Kulon Progo',
        'geom_table'    => 'polaruang_kp',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'polaruang_kp_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'polaruang-sleman' => [
        'group_key'     => 'rtrw',
        'label'         => 'RTRW Kab. Sleman',
        'geom_table'    => 'polaruang_sleman',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'polaruang_sleman_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'rtrw-diy' => [
        'group_key'     => 'rtrw',
        'label'         => 'RTRW D.I. Yogyakarta',
        'geom_table'    => 'rtrw_diy',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'rtrw_diy_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],

    // ── RDTR ────────────────────────────────────────────────────────────────
    'rdtr-kota' => [
        'group_key'     => 'rdtr',
        'label'         => 'RDTR Kota Yogyakarta',
        'geom_table'    => 'rdtr_kota',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'rdtr_kota_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'rdtr-bantul' => [
        'group_key'     => 'rdtr',
        'label'         => 'RDTR Kab. Bantul',
        'geom_table'    => 'rdtr_bantul',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'rdtr_bantul_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'rdtr-sleman' => [
        'group_key'     => 'rdtr',
        'label'         => 'RDTR Kab. Sleman',
        'geom_table'    => 'rdtr_sleman',
        'geom_pk'       => 'gid',
        'kawasan_table' => 'rdtr_sleman_kawasan',
        'kawasan_fk'    => 'id_kawasan',
    ],
    'rdtr-diy' => [
        'group_key'     => 'rdtr',
        'label'         => 'RDTR D.I. Yogyakarta',
        'geom_table'    => 'rdtr_diy',
        'geom_pk'       => 'gid',
        'kawasan_table' => null,   // flat — tidak ada sub-item kawasan
        'kawasan_fk'    => null,
    ],

];
