<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = true;

    protected $table = 'bidang';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $fillable = [
        'id_persil',
        'id_jenis_hak',
        'id_jenis_hak_adat',
        'id_kategori',
        'id_status_kesesuaian',
        'id_pengelola',
        'id_penggunaan',
        'pemilik',
        'nomor_hak',
        'nomor_hak_adat',
        'nomor_bidang',
        'luas',
        'geom',
        'koordinat',
        'id_file',
        'keterangan',
        'created_by',
        'verified_by',
    ];

    public function labels(): array
    {
        return [
            'id_persil'             => 'Persil',
            'id_jenis_hak'          => 'Jenis Hak',
            'id_jenis_hak_adat'     => 'Jenis Hak Adat',
            'id_kategori'           => 'Kategori',
            'id_status_kesesuaian'  => 'Status Kesesuaian',
            'id_pengelola'          => 'Pengelola',
            'id_penggunaan'         => 'Penggunaan',
            'pemilik'               => 'Pemilik',
            'nomor_hak'             => 'Nomor Hak',
            'nomor_hak_adat'        => 'Nomor Hak Adat',
            'nomor_bidang'          => 'No. Bidang',
            'luas'                  => 'Luas (m²)',
            'geom'                  => 'Geometri',
            'koordinat'             => 'Koordinat',
            'id_file'               => 'File Dokumen',
            'keterangan'            => 'Keterangan',
        ];
    }

    public function rules($scenario = null): array
    {
        $base = [
            'id_persil'             => ['nullable', 'integer', 'exists:persil,id'],
            'id_jenis_hak'          => ['nullable', 'integer', 'exists:jenis_hak,id'],
            'id_jenis_hak_adat'     => ['nullable', 'integer', 'exists:jenis_hak_adat,id'],
            'id_kategori'           => ['nullable', 'integer', 'exists:kategori,id'],
            'id_status_kesesuaian'  => ['nullable', 'integer', 'exists:status_kesesuaian,id'],
            'id_pengelola'          => ['nullable', 'integer', 'exists:pengelola,id'],
            'id_penggunaan'         => ['nullable', 'integer', 'exists:penggunaan,id'],
            'pemilik'               => ['nullable', 'in:kasultanan,kadipaten'],
            'nomor_hak'             => ['nullable', 'string', 'max:64'],
            'nomor_hak_adat'        => ['nullable', 'string', 'max:64'],
            'nomor_bidang'          => ['required', 'string', 'max:64'],
            'luas'                  => ['nullable', 'numeric'],
            'geom'                  => ['nullable'],
            'koordinat'             => ['nullable', 'string', 'max:255'],
            'id_file'               => ['nullable', 'integer', 'exists:files,id'],
            'keterangan'            => ['nullable', 'string', 'max:512'],
        ];

        return $base;
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function persil(): BelongsTo
    {
        return $this->belongsTo(Persil::class, 'id_persil');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function jenisHak(): BelongsTo
    {
        return $this->belongsTo(JenisHak::class, 'id_jenis_hak');
    }

    public function jenisHakAdat(): BelongsTo
    {
        return $this->belongsTo(JenisHakAdat::class, 'id_jenis_hak_adat');
    }

    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(Pengelola::class, 'id_pengelola');
    }

    public function penggunaan(): BelongsTo
    {
        // Tabel: penggunaan (dikelola oleh PenggunaanRDTR model)
        return $this->belongsTo(PenggunaanRDTR::class, 'id_penggunaan');
    }

    public function statusKesesuaian(): BelongsTo
    {
        return $this->belongsTo(StatusKesesuaian::class, 'id_status_kesesuaian');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function subPersils(): HasMany
    {
        return $this->hasMany(SubPersil::class, 'id_bidang');
    }

    public function galeris(): HasMany
    {
        return $this->hasMany(GaleriBidang::class, 'id_bidang');
    }
}
