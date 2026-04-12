<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class IdmcKawasanStrategisKasultanan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_kawasan_strategis_kasultanan';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'objectid',
        'satuan_rua',
        'geom',
    ];

    public function labels(): array
    {
        return [
            'objectid'   => 'Object ID',
            'satuan_rua' => 'Satuan Rua',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'objectid'   => ['nullable', 'numeric'],
            'satuan_rua' => ['nullable', 'string', 'max:254'],
            'geom'       => ['nullable'],
        ];
    }
}
