<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KepemilikanTanah extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kepemilikan_tanah';

    protected $fillable = ['nama', 'jenis'];

    public function labels(): array
    {
        return [
            'nama'  => 'Nama',
            'jenis' => 'Jenis',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'  => ['required', 'string', 'max:50'],
            'jenis' => ['nullable', 'integer'],
        ];
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_kepemilikan_tanah');
    }

    public function pengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(PengajuanTanahDesa::class, 'id_kepemilikan_tanah');
    }
}
