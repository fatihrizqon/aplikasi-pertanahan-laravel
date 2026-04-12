<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// -----------------------------------------------
class RtrwDiyKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rtrw_diy_kawasan';

    protected $fillable = ['id', 'warna', 'nama'];

    public function labels(): array
    {
        return ['id' => 'ID', 'warna' => 'Warna', 'nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'warna' => ['nullable', 'string', 'max:7'],
            'nama'  => ['nullable', 'string', 'max:32'],
        ];
    }

    public function rtrwDiys(): HasMany
    {
        return $this->hasMany(RtrwDiy::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class RtrwDiy extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'rtrw_diy';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'pola_iv', 'pola_iii', 'pola_ii', 'pola_i', 'luas_ha',
        'nama_kwsn', 'geom', 'id_kawasan',
    ];

    public function labels(): array
    {
        return [
            'pola_i'     => 'Pola I',
            'nama_kwsn'  => 'Nama Kawasan',
            'luas_ha'    => 'Luas (Ha)',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'pola_iv'   => ['nullable', 'string', 'max:50'],
            'pola_iii'  => ['nullable', 'string', 'max:50'],
            'pola_ii'   => ['nullable', 'string', 'max:50'],
            'pola_i'    => ['nullable', 'string', 'max:50'],
            'luas_ha'   => ['nullable', 'numeric'],
            'nama_kwsn' => ['nullable', 'string', 'max:50'],
            'geom'      => ['nullable'],
            'id_kawasan'=> ['nullable', 'integer', 'exists:rtrw_diy_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(RtrwDiyKawasan::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangBantulKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_bantul_kawasan';

    protected $fillable = ['id', 'warna', 'nama'];

    public function labels(): array
    {
        return ['id' => 'ID', 'warna' => 'Warna', 'nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'warna' => ['nullable', 'string', 'max:7'],
            'nama'  => ['nullable', 'string', 'max:32'],
        ];
    }

    public function polaruangBantuls(): HasMany
    {
        return $this->hasMany(PolaruangBantul::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangBantul extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_bantul';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'fid_polaru', 'id', 'kawasan', 'guna_lahan', 'fid_insteb',
        'provinsi', 'kecamatan', 'kode_kec', 'shape_le_1', 'shape_area',
        'keterangan', 'geom', 'id_kawasan', 'nama',
    ];

    public function labels(): array
    {
        return [
            'kawasan'    => 'Kawasan',
            'guna_lahan' => 'Guna Lahan',
            'kecamatan'  => 'Kecamatan',
            'nama'       => 'Nama',
            'id_kawasan' => 'Kawasan Ref',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'fid_polaru' => ['nullable', 'integer'],
            'id'         => ['nullable', 'numeric'],
            'kawasan'    => ['nullable', 'string', 'max:50'],
            'guna_lahan' => ['nullable', 'string', 'max:50'],
            'fid_insteb' => ['nullable', 'integer'],
            'provinsi'   => ['nullable', 'string', 'max:30'],
            'kecamatan'  => ['nullable', 'string', 'max:18'],
            'kode_kec'   => ['nullable', 'integer'],
            'shape_le_1' => ['nullable', 'numeric'],
            'shape_area' => ['nullable', 'numeric'],
            'keterangan' => ['nullable', 'string', 'max:50'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:polaruang_bantul_kawasan,id'],
            'nama'       => ['nullable', 'string', 'max:32'],
        ];
    }

    public function kawasanRef(): BelongsTo
    {
        return $this->belongsTo(PolaruangBantulKawasan::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangGkKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_gk_kawasan';

    protected $fillable = ['id', 'warna', 'nama'];

    public function labels(): array
    {
        return ['id' => 'ID', 'warna' => 'Warna', 'nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'warna' => ['nullable', 'string', 'max:7'],
            'nama'  => ['nullable', 'string', 'max:32'],
        ];
    }

    public function polaruangGks(): HasMany
    {
        return $this->hasMany(PolaruangGk::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangGk extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_gk';
    protected $primaryKey = 'gid';

    protected $fillable = ['pola_ruang', 'geom', 'id_kawasan'];

    public function labels(): array
    {
        return [
            'pola_ruang' => 'Pola Ruang',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'pola_ruang' => ['nullable', 'string', 'max:254'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:polaruang_gk_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(PolaruangGkKawasan::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangKpKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_kp_kawasan';

    protected $fillable = ['id', 'warna', 'nama'];

    public function labels(): array
    {
        return ['id' => 'ID', 'warna' => 'Warna', 'nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'warna' => ['nullable', 'string', 'max:7'],
            'nama'  => ['nullable', 'string', 'max:32'],
        ];
    }

    public function polaruangKps(): HasMany
    {
        return $this->hasMany(PolaruangKp::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangKp extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_kp';
    protected $primaryKey = 'gid';

    protected $fillable = [
        'pola_ruang', 'fungsi', 'k_budidaya', 'k_lindung', 'kws_genera', 'geom', 'id_kawasan',
    ];

    public function labels(): array
    {
        return [
            'pola_ruang' => 'Pola Ruang',
            'fungsi'     => 'Fungsi',
            'k_budidaya' => 'Kawasan Budidaya',
            'k_lindung'  => 'Kawasan Lindung',
            'kws_genera' => 'Kawasan General',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'pola_ruang' => ['nullable', 'string', 'max:254'],
            'fungsi'     => ['nullable', 'string', 'max:200'],
            'k_budidaya' => ['nullable', 'string', 'max:200'],
            'k_lindung'  => ['nullable', 'string', 'max:200'],
            'kws_genera' => ['nullable', 'string', 'max:50'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:polaruang_kp_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(PolaruangKpKawasan::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangSlemanKawasan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_sleman_kawasan';

    protected $fillable = ['id', 'warna', 'nama'];

    public function labels(): array
    {
        return ['id' => 'ID', 'warna' => 'Warna', 'nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'    => ['required', 'integer'],
            'warna' => ['nullable', 'string', 'max:7'],
            'nama'  => ['nullable', 'string', 'max:32'],
        ];
    }

    public function polaruangSlemans(): HasMany
    {
        return $this->hasMany(PolaruangSleman::class, 'id_kawasan');
    }
}

// -----------------------------------------------
class PolaruangSleman extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;
    protected $table = 'polaruang_sleman';
    protected $primaryKey = 'gid';

    protected $fillable = ['keterangan', 'geom', 'id_kawasan'];

    public function labels(): array
    {
        return [
            'keterangan' => 'Keterangan',
            'id_kawasan' => 'Kawasan',
            'geom'       => 'Geometri',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'keterangan' => ['nullable', 'string', 'max:40'],
            'geom'       => ['nullable'],
            'id_kawasan' => ['nullable', 'integer', 'exists:polaruang_sleman_kawasan,id'],
        ];
    }

    public function kawasan(): BelongsTo
    {
        return $this->belongsTo(PolaruangSlemanKawasan::class, 'id_kawasan');
    }
}
