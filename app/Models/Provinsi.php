<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'provinsi';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'geom',
    ];

    public function labels(): array
    {
        return [
            'id'   => 'ID',
            'kode' => 'Kode',
            'nama' => 'Nama',
            'geom' => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'   => ['required', 'integer'],
            'kode' => ['required', 'string', 'max:8'],
            'nama' => ['required', 'string', 'max:64'],
            'geom' => ['nullable'],
        ];
    }

    public function kabupatens(): HasMany
    {
        return $this->hasMany(Kabupaten::class, 'id_provinsi');
    }
}
