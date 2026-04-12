<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kategori';

    protected $fillable = [
        'id',
        'nama',
        'warna',
    ];

    public function labels(): array
    {
        return [
            'id'    => 'ID',
            'nama'  => 'Nama',
            'warna' => 'Warna',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'nama'  => ['required', 'string', 'max:32'],
            'warna' => ['nullable', 'string', 'max:12'],
        ];
    }

    public function persils(): HasMany
    {
        return $this->hasMany(Persil::class, 'id_kategori');
    }
}
