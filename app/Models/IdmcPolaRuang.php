<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdmcPolaRuang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_pola_ruang';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'pola_iv', 'pola_iii', 'pola_ii', 'pola_i', 'nama_kwsn',
        'wadmkc', 'wadmkk', 'wadmpr', 'kecamata_1', 'kabupaten',
        'wilayah', 'ket_kra', 'sumber_kra', 'nama_kbak', 'geom', 'id_jenis',
    ];

    public function labels(): array
    {
        return [
            'pola_i'    => 'Pola I',
            'pola_ii'   => 'Pola II',
            'pola_iii'  => 'Pola III',
            'pola_iv'   => 'Pola IV',
            'nama_kwsn' => 'Nama Kawasan',
            'kabupaten' => 'Kabupaten',
            'id_jenis'  => 'Jenis',
            'geom'      => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'pola_iv'    => ['nullable', 'string', 'max:50'],
            'pola_iii'   => ['nullable', 'string', 'max:50'],
            'pola_ii'    => ['nullable', 'string', 'max:50'],
            'pola_i'     => ['nullable', 'string', 'max:50'],
            'nama_kwsn'  => ['nullable', 'string', 'max:50'],
            'wadmkc'     => ['nullable', 'string', 'max:50'],
            'wadmkk'     => ['nullable', 'string', 'max:50'],
            'wadmpr'     => ['nullable', 'string', 'max:50'],
            'kecamata_1' => ['nullable', 'string', 'max:50'],
            'kabupaten'  => ['nullable', 'string', 'max:30'],
            'wilayah'    => ['nullable', 'string', 'max:50'],
            'ket_kra'    => ['nullable', 'string', 'max:50'],
            'sumber_kra' => ['nullable', 'string', 'max:50'],
            'nama_kbak'  => ['nullable', 'string', 'max:50'],
            'geom'       => ['nullable'],
            'id_jenis'   => ['nullable', 'integer', 'exists:idmc_pola_ruang_jenis,id'],
        ];
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(IdmcPolaRuangJenis::class, 'id_jenis');
    }
}
