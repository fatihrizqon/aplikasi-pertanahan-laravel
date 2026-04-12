<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class IdmcStrukturRuangJaringan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_struktur_ruang_jaringan';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'objectid', 'nama', 'keterangan', 'ruas_jalur', 'panjang_km',
        'kewenangan', 'fungsi', 'rencana', 'air', 'jenis',
        'sumber', 'kondisi', 'handle', 'geom',
    ];

    public function labels(): array
    {
        return [
            'nama'       => 'Nama',
            'keterangan' => 'Keterangan',
            'ruas_jalur' => 'Ruas Jalur',
            'panjang_km' => 'Panjang (km)',
            'kewenangan' => 'Kewenangan',
            'fungsi'     => 'Fungsi',
            'jenis'      => 'Jenis',
            'kondisi'    => 'Kondisi',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'objectid'   => ['nullable', 'numeric'],
            'nama'       => ['nullable', 'string', 'max:50'],
            'keterangan' => ['nullable', 'string', 'max:50'],
            'ruas_jalur' => ['nullable', 'string', 'max:50'],
            'panjang_km' => ['nullable', 'numeric'],
            'kewenangan' => ['nullable', 'string', 'max:50'],
            'fungsi'     => ['nullable', 'string', 'max:50'],
            'rencana'    => ['nullable', 'string', 'max:50'],
            'air'        => ['nullable', 'string', 'max:50'],
            'jenis'      => ['nullable', 'string', 'max:50'],
            'sumber'     => ['nullable', 'string', 'max:50'],
            'kondisi'    => ['nullable', 'string', 'max:50'],
            'handle'     => ['nullable', 'string', 'max:16'],
            'geom'       => ['nullable'],
        ];
    }
}
