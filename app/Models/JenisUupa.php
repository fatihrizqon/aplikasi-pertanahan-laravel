<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisUupa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'jenis_uupa';

    protected $fillable = [
        'id',
        'nama',
        'warna',
        'ontop',
    ];

    public function labels(): array
    {
        return [
            'id'    => 'ID',
            'nama'  => 'Nama',
            'warna' => 'Warna',
            'ontop' => 'On Top',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'nama'  => ['required', 'string', 'max:32'],
            'warna' => ['nullable', 'string', 'max:15'],
            'ontop' => ['nullable', 'integer'],
        ];
    }

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_jenis_uupa');
    }
}
