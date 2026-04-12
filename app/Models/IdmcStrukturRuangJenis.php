<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdmcStrukturRuangJenis extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_struktur_ruang_jenis';

    protected $fillable = ['id', 'nama', 'warna'];

    public function labels(): array
    {
        return ['id' => 'ID', 'nama' => 'Nama', 'warna' => 'Warna'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'nama'  => ['nullable', 'string', 'max:100'],
            'warna' => ['nullable', 'string', 'max:7'],
        ];
    }

    public function strukturRuangs(): HasMany
    {
        return $this->hasMany(IdmcStrukturRuang::class, 'id_jenis');
    }
}
