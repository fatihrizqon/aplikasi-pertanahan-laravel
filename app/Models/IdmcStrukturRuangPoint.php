<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class IdmcStrukturRuangPoint extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_struktur_ruang_point';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'nama', 'lokasi', 'keterangan', 'jenis',
        'hirarki', 'kondisi', 'status_1', 'geom',
    ];

    public function labels(): array
    {
        return [
            'nama'       => 'Nama',
            'lokasi'     => 'Lokasi',
            'keterangan' => 'Keterangan',
            'jenis'      => 'Jenis',
            'hirarki'    => 'Hirarki',
            'kondisi'    => 'Kondisi',
            'status_1'   => 'Status',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'       => ['nullable', 'string', 'max:50'],
            'lokasi'     => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string', 'max:50'],
            'jenis'      => ['nullable', 'string', 'max:50'],
            'hirarki'    => ['nullable', 'string', 'max:50'],
            'kondisi'    => ['nullable', 'string', 'max:50'],
            'status_1'   => ['nullable', 'string', 'max:50'],
            'geom'       => ['nullable'],
        ];
    }
}
