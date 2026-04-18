<?php

namespace App\Models;

use App\Models\Bidang;
use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusKesesuaian extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'status_kesesuaian';

    protected $fillable = [
        'nama',
        'warna',
    ];

    public function labels(): array
    {
        return [
            'nama'  => 'Nama',
            'warna' => 'Warna',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'  => ['nullable', 'string', 'max:256'],
            'warna' => ['nullable', 'string', 'max:15'],
        ];
    }

    public function bidang(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_penggunaan');
    }

    public function bidangsKesesuaian(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_status_kesesuaian');
    }

    public function bidangsRdtr(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_kesesuaian_rdtr');
    }
}
