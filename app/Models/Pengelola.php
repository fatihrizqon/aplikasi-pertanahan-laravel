<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Pengelola extends Model
{
    use ModelTrait, ValidatableTrait, Searchable;

    public $timestamps = false;

    protected $table = 'pengelola';

    protected $fillable = [
        'nama',
        'keterangan',
        'kontak',
        'no_telepon',
        'email',
        'alamat',
    ];

    public function labels(): array
    {
        return [
            'nama'        => 'Nama',
            'keterangan'  => 'Keterangan',
            'kontak'      => 'Kontak',
            'no_telepon'  => 'No. Telepon',
            'email'       => 'Email',
            'alamat'      => 'Alamat',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'       => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:512'],
            'kontak'     => ['nullable', 'string', 'max:64'],
            'no_telepon' => ['nullable', 'string', 'max:18'],
            'email'      => ['nullable', 'email', 'max:64'],
            'alamat'     => ['nullable', 'string', 'max:255'],
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
        return $this->hasMany(Bidang::class, 'id_pengelola');
    }

    public function sub_persil(): HasMany
    {
        return $this->hasMany(SubPersil::class, 'id_pengelola');
    }
}
