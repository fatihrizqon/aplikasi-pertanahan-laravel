<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use App\Models\Bidang;

class JenisHakAdat extends Model
{
    use ModelTrait, ValidatableTrait, Searchable;

    public $timestamps = false;

    protected $table = 'jenis_hak_adat';

    protected $fillable = [
        'nama',
        'warna',
    ];

    public function labels(): array
    {
        return [
            'nama'       => 'Nama',
            'warna'      => 'Warna',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'       => ['required', 'string', 'max:32'],
            'warna'      => ['nullable', 'string', 'max:15'],
        ];
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'nama' => $this->nama,
            'keterangan' => $this->keterangan,
        ];
    }

    public function bidang(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_jenis_hak_adat');
    }
}
