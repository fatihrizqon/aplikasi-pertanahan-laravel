<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kecamatan';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $fillable = [
        'id',
        'id_kabupaten',
        'kode',
        'nama',
        'geom',
        'id_kecamatan',
    ];

    public function labels(): array
    {
        return [
            'id'           => 'ID',
            'id_kabupaten' => 'Kabupaten',
            'kode'         => 'Kode',
            'nama'         => 'Nama',
            'id_kecamatan' => 'ID Kecamatan',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'           => ['required', 'integer'],
            'id_kabupaten' => ['required', 'integer', 'exists:kabupaten,id'],
            'kode'         => ['nullable', 'string', 'max:8'],
            'nama'         => ['required', 'string', 'max:64'],
            'geom'         => ['nullable'],
            'id_kecamatan' => ['nullable', 'integer'],
        ];
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten');
    }

    public function kelurahans(): HasMany
    {
        return $this->hasMany(Kelurahan::class, 'id_kecamatan');
    }
}
