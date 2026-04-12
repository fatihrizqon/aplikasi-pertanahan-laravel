<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class PenggunaanTKD extends Model
{
    use ModelTrait, ValidatableTrait, Searchable;

    public $timestamps = false;

    protected $table = 'penggunaan_tanah_desa';

    protected $fillable = [
        'nama',
        'nama_file',
        'warna',
        'ontop',
    ];

    public function labels(): array
    {
        return [
            'nama'      => 'Nama',
            'nama_file' => 'Nama File',
            'warna'     => 'Warna',
            'ontop'     => 'On Top',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'      => ['required', 'string', 'max:64'],
            'nama_file' => ['nullable', 'string', 'max:256'],
            'warna'     => ['nullable', 'string', 'max:15'],
            'ontop'     => ['nullable', 'integer'],
        ];
    }

    public function pengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(PengajuanTanahDesa::class, 'id_penggunaan');
    }
}
