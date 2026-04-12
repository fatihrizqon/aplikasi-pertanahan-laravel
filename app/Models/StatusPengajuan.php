<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPengajuan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'status_pengajuan';

    protected $fillable = ['nama', 'warna'];

    public function labels(): array
    {
        return [
            'nama'  => 'Nama',
            'warna' => 'Warna',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'  => ['required', 'string', 'max:100'],
            'warna' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_status_pengajuan');
    }

    public function pengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(PengajuanTanahDesa::class, 'id_status_pengajuan');
    }
}
