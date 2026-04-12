<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdmcStrukturRuang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_struktur_ruang';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'objectid', 'ket_resapa', 'smbr_resap', 'wa_1', 'ta_1',
        'kabupaten', 'kewenangan', 'nama', 'sumber_cat', 'nama_cat',
        'ket_cat', 'wadmkc', 'wadmkk', 'nama_das_1', 'sumber_das',
        'nama_waduk', 'ket_waduk', 'shape_leng', 'shape_area',
        'geom', 'id_jenis',
    ];

    public function labels(): array
    {
        return [
            'nama'       => 'Nama',
            'kabupaten'  => 'Kabupaten',
            'kewenangan' => 'Kewenangan',
            'id_jenis'   => 'Jenis',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'objectid'   => ['nullable', 'numeric'],
            'ket_resapa' => ['nullable', 'string', 'max:50'],
            'smbr_resap' => ['nullable', 'string', 'max:50'],
            'wa_1'       => ['nullable', 'string', 'max:40'],
            'ta_1'       => ['nullable', 'string', 'max:15'],
            'kabupaten'  => ['nullable', 'string', 'max:15'],
            'kewenangan' => ['nullable', 'string', 'max:15'],
            'nama'       => ['nullable', 'string', 'max:50'],
            'sumber_cat' => ['nullable', 'string', 'max:30'],
            'nama_cat'   => ['nullable', 'string', 'max:30'],
            'ket_cat'    => ['nullable', 'string', 'max:30'],
            'wadmkc'     => ['nullable', 'string', 'max:50'],
            'wadmkk'     => ['nullable', 'string', 'max:50'],
            'nama_das_1' => ['nullable', 'string', 'max:50'],
            'sumber_das' => ['nullable', 'string', 'max:50'],
            'nama_waduk' => ['nullable', 'string', 'max:30'],
            'ket_waduk'  => ['nullable', 'string', 'max:20'],
            'shape_leng' => ['nullable', 'numeric'],
            'shape_area' => ['nullable', 'numeric'],
            'geom'       => ['nullable'],
            'id_jenis'   => ['nullable', 'integer', 'exists:idmc_struktur_ruang_jenis,id'],
        ];
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(IdmcStrukturRuangJenis::class, 'id_jenis');
    }
}
