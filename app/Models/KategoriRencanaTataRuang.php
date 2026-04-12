<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriRencanaTataRuang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kategori_rencana_tata_ruang';

    protected $fillable = [
        'nama',
        'warna',
        'ontop',
    ];

    public function labels(): array
    {
        return [
            'nama'  => 'Nama',
            'warna' => 'Warna',
            'ontop' => 'On Top',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'  => ['nullable', 'string', 'max:256'],
            'warna' => ['nullable', 'string', 'max:15'],
            'ontop' => ['required', 'integer'],
        ];
    }

    public function rencanaTataRuangs(): HasMany
    {
        return $this->hasMany(RencanaTataRuang::class, 'id_kategori_rencana_tata_ruang');
    }
}
