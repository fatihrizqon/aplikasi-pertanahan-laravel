<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdmcKawasanStrategis extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_kawasan_strategis';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'kabupaten', 'kecamatan', 'desa', 'koridor', 'wp',
        'wadmkc', 'wadmkk', 'keterangan', 'luas_ha', 'nama',
        'ket', 'keterang_1', 'keta', 'wadmkc_1', 'wadmkk_1',
        'ket_1', 'geom', 'id_jenis',
    ];

    public function labels(): array
    {
        return [
            'kabupaten'  => 'Kabupaten',
            'kecamatan'  => 'Kecamatan',
            'desa'       => 'Desa',
            'nama'       => 'Nama',
            'luas_ha'    => 'Luas (Ha)',
            'keterangan' => 'Keterangan',
            'id_jenis'   => 'Jenis',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'kabupaten'  => ['nullable', 'string', 'max:50'],
            'kecamatan'  => ['nullable', 'string', 'max:50'],
            'desa'       => ['nullable', 'string', 'max:50'],
            'koridor'    => ['nullable', 'string', 'max:50'],
            'wp'         => ['nullable', 'string', 'max:50'],
            'wadmkc'     => ['nullable', 'string', 'max:50'],
            'wadmkk'     => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string', 'max:50'],
            'luas_ha'    => ['nullable', 'numeric'],
            'nama'       => ['nullable', 'string', 'max:30'],
            'ket'        => ['nullable', 'string', 'max:50'],
            'keterang_1' => ['nullable', 'string', 'max:50'],
            'keta'       => ['nullable', 'string', 'max:75'],
            'wadmkc_1'   => ['nullable', 'string', 'max:50'],
            'wadmkk_1'   => ['nullable', 'string', 'max:50'],
            'ket_1'      => ['nullable', 'string', 'max:50'],
            'geom'       => ['nullable'],
            'id_jenis'   => ['nullable', 'integer', 'exists:idmc_kawasan_strategis_jenis,id'],
        ];
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(IdmcKawasanStrategisJenis::class, 'id_jenis');
    }
}
