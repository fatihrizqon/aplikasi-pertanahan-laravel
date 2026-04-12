<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kategori_tanah_desa';

    protected $fillable = [
        'nama',
    ];

    public function labels(): array
    {
        return [
            'nama' => 'Nama',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:32'],
        ];
    }

    public function persils(): HasMany
    {
        return $this->hasMany(Persil::class, 'id_kategori_tanah_desa');
    }
}
