<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LampiranKategori extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'lampiran_kategori';

    protected $fillable = ['nama'];

    public function labels(): array
    {
        return ['nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return ['nama' => ['required', 'string', 'max:50']];
    }

    public function lampiranJenis(): HasMany
    {
        return $this->hasMany(LampiranJenis::class, 'id_lampiran_kategori');
    }
}
