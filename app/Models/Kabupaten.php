<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kabupaten extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kabupaten';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $fillable = [
        'id',
        'id_provinsi',
        'kode',
        'nama',
        'geom',
        'id_kabupaten',
        'kode_surat',
    ];

    public function labels(): array
    {
        return [
            'id'           => 'ID',
            'id_provinsi'  => 'Provinsi',
            'kode'         => 'Kode',
            'nama'         => 'Nama',
            'id_kabupaten' => 'ID Kabupaten',
            'kode_surat'   => 'Kode Surat',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'           => ['required', 'integer'],
            'id_provinsi'  => ['required', 'integer', 'exists:provinsi,id'],
            'kode'         => ['nullable', 'string', 'max:8'],
            'nama'         => ['required', 'string', 'max:64'],
            'geom'         => ['nullable'],
            'id_kabupaten' => ['nullable', 'integer'],
            'kode_surat'   => ['nullable', 'string', 'max:8'],
        ];
    }

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi');
    }

    public function kecamatans(): HasMany
    {
        return $this->hasMany(Kecamatan::class, 'id_kabupaten');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_kabupaten');
    }
}
