<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// -----------------------------------------------
class RdtrKotaKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_kota_kawasan';

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

    public function rdtrKotas(): HasMany
    {
        return $this->hasMany(RdtrKota::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class RdtrKota extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_kota';
    protected $primaryKey = 'gid';

    protected $fillable = ['symbolid', 'sub_zona', 'geom', 'id_kawasan'];

    public function labels(): array
    {
        return [
            'symbolid'   => 'Symbol ID',
            'sub_zona'   => 'Sub Zona',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'symbolid'   => ['nullable', 'numeric'],
            'sub_zona'   => ['nullable', 'string', 'max:50'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:rdtr_kota_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(RdtrKotaKawasan::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class RdtrSlemanKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_sleman_kawasan';

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

    public function rdtrSlemans(): HasMany
    {
        return $this->hasMany(RdtrSleman::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class RdtrSleman extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rdtr_sleman';
    protected $primaryKey = 'gid';

    protected $fillable = ['kecamatan', 'peruntukan', 'geom', 'id_kawasan'];

    public function labels(): array
    {
        return [
            'kecamatan'  => 'Kecamatan',
            'peruntukan' => 'Peruntukan',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'kecamatan'  => ['nullable', 'string', 'max:50'],
            'peruntukan' => ['nullable', 'string', 'max:75'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:rdtr_sleman_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(RdtrSlemanKawasan::class, 'id_kawasan');
    }
}
