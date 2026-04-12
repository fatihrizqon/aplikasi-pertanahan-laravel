<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kelurahan';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $fillable = [
        'id',
        'id_kecamatan',
        'kode',
        'nama',
        'geom',
    ];

    public function labels(): array
    {
        return [
            'id'           => 'ID',
            'id_kecamatan' => 'Kecamatan',
            'kode'         => 'Kode',
            'nama'         => 'Nama',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'           => ['required', 'integer'],
            'id_kecamatan' => ['required', 'integer', 'exists:kecamatan,id'],
            'kode'         => ['required', 'string', 'max:8'],
            'nama'         => ['required', 'string', 'max:64'],
            'geom'         => ['nullable'],
        ];
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    }

    public function persils(): HasMany
    {
        return $this->hasMany(Persil::class, 'id_kelurahan');
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_kelurahan');
    }

    public function pengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(PengajuanTanahDesa::class, 'id_kelurahan');
    }
}
