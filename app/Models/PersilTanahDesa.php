<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersilTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'persil_tanah_desa';

    protected $fillable = [
        'id_pengajuan_tanah_desa',
        'persil',
        'bidang',
        'sub_persil',
    ];

    public function labels(): array
    {
        return [
            'id_pengajuan_tanah_desa' => 'Pengajuan Tanah Desa',
            'persil'                  => 'Persil',
            'bidang'                  => 'Bidang',
            'sub_persil'              => 'Sub Persil',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan_tanah_desa' => ['required', 'integer', 'exists:pengajuan_tanah_desa,id'],
            'persil'                  => ['nullable', 'string', 'max:64'],
            'bidang'                  => ['nullable', 'string', 'max:20'],
            'sub_persil'              => ['nullable', 'string', 'max:20'],
        ];
    }

    public function pengajuanTanahDesa(): BelongsTo
    {
        return $this->belongsTo(PengajuanTanahDesa::class, 'id_pengajuan_tanah_desa');
    }
}
