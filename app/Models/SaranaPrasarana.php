<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaranaPrasarana extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'sarana_prasarana';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'nama_fasum',
        'jenis',
        'kategori',
        'nama_foto',
        'link_foto',
        'keterangan',
        'id',
        'id_kategori_sarana_prasarana',
        'id_kabupaten',
        'geom',
    ];

    public function labels(): array
    {
        return [
            'nama_fasum'                   => 'Nama Fasum',
            'jenis'                        => 'Jenis',
            'kategori'                     => 'Kategori',
            'nama_foto'                    => 'Nama Foto',
            'link_foto'                    => 'Link Foto',
            'keterangan'                   => 'Keterangan',
            'id_kategori_sarana_prasarana' => 'Kategori Sarana Prasarana',
            'id_kabupaten'                 => 'Kabupaten',
            'geom'                         => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama_fasum'                   => ['nullable', 'string', 'max:254'],
            'jenis'                        => ['nullable', 'string', 'max:254'],
            'kategori'                     => ['nullable', 'string', 'max:254'],
            'nama_foto'                    => ['nullable', 'string', 'max:254'],
            'link_foto'                    => ['nullable', 'string', 'max:254'],
            'keterangan'                   => ['nullable', 'string', 'max:254'],
            'id'                           => ['nullable', 'string', 'max:254'],
            'id_kategori_sarana_prasarana' => ['nullable', 'integer', 'exists:kategori_sarana_prasarana,id'],
            'id_kabupaten'                 => ['nullable', 'integer', 'exists:kabupaten,id'],
            'geom'                         => ['nullable'],
        ];
    }

    public function kategoriSaranaPrasarana(): BelongsTo
    {
        return $this->belongsTo(KategoriSaranaPrasarana::class, 'id_kategori_sarana_prasarana');
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten');
    }
}
