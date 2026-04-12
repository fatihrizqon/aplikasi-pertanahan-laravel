<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TujuanPermohonan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'tujuan_permohonan';

    protected $fillable = ['nama'];

    public function labels(): array
    {
        return ['nama' => 'Nama'];
    }

    public function rules($scenario = null): array
    {
        return ['nama' => ['required', 'string', 'max:50']];
    }

    public function pengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(PengajuanTanahDesa::class, 'id_tujuan_permohonan');
    }

    public function lampiranJenisTanahDesas(): HasMany
    {
        return $this->hasMany(LampiranJenisTanahDesa::class, 'id_tujuan_permohonan');
    }
}
