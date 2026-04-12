<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPengajuan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'jenis_pengajuan';

    protected $fillable = ['nama'];

    public function labels(): array
    {
        return ['nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return ['nama' => ['required', 'string', 'max:50']];
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_jenis_pengajuan');
    }

    public function pengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(PengajuanTanahDesa::class, 'id_jenis_pengajuan');
    }
}
