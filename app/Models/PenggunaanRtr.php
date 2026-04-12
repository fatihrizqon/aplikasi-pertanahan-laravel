<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenggunaanRtr extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'penggunaan_rtr';

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

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_penggunaan');
    }

    public function subPersils(): HasMany
    {
        return $this->hasMany(SubPersil::class, 'id_penggunaan');
    }

    public function penggunaanSgs(): HasMany
    {
        return $this->hasMany(PenggunaanSg::class, 'id_penggunaan');
    }
}
