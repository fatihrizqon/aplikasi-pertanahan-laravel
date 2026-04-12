<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdmcKawasanStrategisJenis extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'idmc_kawasan_strategis_jenis';

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

    public function kawasanStrategis(): HasMany
    {
        return $this->hasMany(IdmcKawasanStrategis::class, 'id_jenis');
    }
}
