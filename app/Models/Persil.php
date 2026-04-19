<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rule;

class Persil extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = true;

    protected $table = 'persil';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $appends = ['lat_long'];

    protected $fillable = [
        'nomor_persil',
        'klas',
        'luas',
        'alamat',
        'id_kelurahan',
        'id_kecamatan',
        'id_kabupaten',
        'batas_utara',
        'batas_selatan',
        'batas_timur',
        'batas_barat',
        'geom',
        'koordinat',
        'legacy_id',
        'created_by',
        'verified_by',
    ];

    public function labels(): array
    {
        return [
            'nomor_persil'  => 'No. Persil',
            'klas'          => 'Klas',
            'luas'          => 'Luas (m²)',
            'alamat'        => 'Alamat',
            'id_kelurahan'  => 'Kelurahan',
            'id_kecamatan'  => 'Kecamatan',
            'id_kabupaten'  => 'Kabupaten',
            'batas_utara'   => 'Batas Utara',
            'batas_selatan' => 'Batas Selatan',
            'batas_timur'   => 'Batas Timur',
            'batas_barat'   => 'Batas Barat',
            'geom'          => 'Geometri',
            'koordinat'     => 'Koordinat',
            'legacy_id'     => 'ID Lama',
        ];
    }

    public function rules($scenario = null): array
    {
        $base = [
            'nomor_persil'  => ['required', 'string', 'max:128', Rule::unique($this->getTable())->ignore($this)],
            'klas'          => ['nullable', 'string', 'max:128'],
            'luas'          => ['nullable', 'numeric'],
            'alamat'        => ['nullable', 'string', 'max:255'],
            'id_kelurahan'  => ['required', 'integer', 'exists:kelurahan,id'],
            'id_kecamatan'  => ['required', 'integer', 'exists:kecamatan,id'],
            'id_kabupaten'  => ['required', 'integer', 'exists:kabupaten,id'],
            'batas_utara'   => ['nullable', 'string', 'max:256'],
            'batas_selatan' => ['nullable', 'string', 'max:256'],
            'batas_timur'   => ['nullable', 'string', 'max:256'],
            'batas_barat'   => ['nullable', 'string', 'max:256'],
            'geom'          => ['nullable'],
            'koordinat'     => ['nullable', 'string'],
            'legacy_id'     => ['nullable', 'string', 'max:64'],
        ];

        return $base;
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_persil');
    }

    public function monitorings(): HasMany
    {
        return $this->hasMany(Monitoring::class, 'id_persil');
    }

    public function getLatLongAttribute(): ?array
    {
        $geom = $this->geom;

        if (!$geom) {
            return null;
        }

        if ($geom instanceof Point) {
            return [
                'lat' => $geom->getLat(),
                'lng' => $geom->getLng(),
            ];
        }

        if (method_exists($geom, 'getCentroid')) {
            $centroid = $geom->getCentroid();

            return [
                'lat' => $centroid->getLat(),
                'lng' => $centroid->getLng(),
            ];
        }

        return null;
    }
}
