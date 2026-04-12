<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// -----------------------------------------------
class RdtrBantulKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_bantul_kawasan';

    protected $fillable = ['id', 'nama', 'warna'];

    public function labels(): array
    {
        return ['id' => 'ID', 'nama' => 'Nama', 'warna' => 'Warna'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'nama'  => ['required', 'string', 'max:100'],
            'warna' => ['required', 'string', 'max:7'],
        ];
    }

    public function rdtrBantuls(): HasMany
    {
        return $this->hasMany(RdtrBantul::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class RdtrBantul extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_bantul';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'objectid', 'pola_ruang', 'ha', 'desa', 'kecamatan', 'geom', 'id_kawasan',
    ];

    public function labels(): array
    {
        return [
            'pola_ruang' => 'Pola Ruang',
            'ha'         => 'Luas (Ha)',
            'desa'       => 'Desa',
            'kecamatan'  => 'Kecamatan',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'objectid'   => ['nullable', 'numeric'],
            'pola_ruang' => ['nullable', 'string', 'max:50'],
            'ha'         => ['nullable', 'numeric'],
            'desa'       => ['nullable', 'string', 'max:30'],
            'kecamatan'  => ['nullable', 'string', 'max:15'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:rdtr_bantul_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(RdtrBantulKawasan::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class RdtrDiy extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_diy';
    protected $primaryKey = 'gid';

    protected $fillable = ['keterangan', 'peruntukan', 'geom'];

    public function labels(): array
    {
        return [
            'keterangan' => 'Keterangan',
            'peruntukan' => 'Peruntukan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'keterangan' => ['nullable', 'string', 'max:50'],
            'peruntukan' => ['nullable', 'string', 'max:100'],
            'geom'       => ['nullable'],
        ];
    }
}
