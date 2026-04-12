<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampiranJenisTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'lampiran_jenis_tanah_desa';

    protected $fillable = [
        'id_lampiran_jenis',
        'id_tujuan_permohonan',
    ];

    public function labels(): array
    {
        return [
            'id_lampiran_jenis'    => 'Jenis Lampiran',
            'id_tujuan_permohonan' => 'Tujuan Permohonan',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_lampiran_jenis'    => ['required', 'integer', 'exists:lampiran_jenis,id'],
            'id_tujuan_permohonan' => ['required', 'integer', 'exists:tujuan_permohonan,id'],
        ];
    }

    public function lampiranJenis(): BelongsTo
    {
        return $this->belongsTo(LampiranJenis::class, 'id_lampiran_jenis');
    }

    public function tujuanPermohonan(): BelongsTo
    {
        return $this->belongsTo(TujuanPermohonan::class, 'id_tujuan_permohonan');
    }
}
