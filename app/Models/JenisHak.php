<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use App\Models\Bidang;

class JenisHak extends Model
{
    use ModelTrait, ValidatableTrait, Searchable;

    public $timestamps = false;

    protected $table = 'jenis_hak';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'warna',
        'ontop',
    ];

    public function labels(): array
    {
        return [
            'kode'       => 'Kode',
            'nama'       => 'Nama',
            'keterangan' => 'Keterangan',
            'warna'      => 'Warna',
            'ontop'      => 'On Top',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'kode'       => ['required', 'string', 'max:16'],
            'nama'       => ['required', 'string', 'max:32'],
            'keterangan' => ['nullable', 'string', 'max:512'],
            'warna'      => ['nullable', 'string', 'max:15'],
            'ontop'      => ['nullable', 'integer'],
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
        return $this->hasMany(Bidang::class, 'id_jenis_hak');
    }
}
