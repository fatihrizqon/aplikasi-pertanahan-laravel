<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RencanaTataRuang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'rencana_tata_ruang';
    protected $primaryKey = 'gid';

    protected $fillable = [
        '____gid',
        'fid_pola_r',
        'fid_batas_',
        'id',
        'fid_edit_1',
        'kws',
        'simbol',
        'luas',
        'fid_kecama',
        'id_1',
        'kecamatan',
        'id_kategori_rencana_tata_ruang',
        'id_kabupaten',
        'geom',
    ];

    public function labels(): array
    {
        return [
            'kws'                            => 'Kawasan',
            'simbol'                         => 'Simbol',
            'luas'                           => 'Luas',
            'kecamatan'                      => 'Kecamatan',
            'id_kategori_rencana_tata_ruang' => 'Kategori RTR',
            'id_kabupaten'                   => 'Kabupaten',
            'geom'                           => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            '____gid'                        => ['nullable', 'integer'],
            'fid_pola_r'                     => ['nullable', 'integer'],
            'fid_batas_'                     => ['nullable', 'integer'],
            'id'                             => ['nullable', 'integer'],
            'fid_edit_1'                     => ['nullable', 'integer'],
            'kws'                            => ['nullable', 'string', 'max:254'],
            'simbol'                         => ['nullable', 'string', 'max:254'],
            'luas'                           => ['nullable', 'numeric'],
            'fid_kecama'                     => ['nullable', 'integer'],
            'id_1'                           => ['nullable', 'integer'],
            'kecamatan'                      => ['nullable', 'string', 'max:254'],
            'id_kategori_rencana_tata_ruang' => ['nullable', 'integer', 'exists:kategori_rencana_tata_ruang,id'],
            'id_kabupaten'                   => ['nullable', 'integer', 'exists:kabupaten,id'],
            'geom'                           => ['nullable'],
        ];
    }

    public function kategoriRencanaTataRuang(): BelongsTo
    {
        return $this->belongsTo(KategoriRencanaTataRuang::class, 'id_kategori_rencana_tata_ruang');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten');
    }
}
