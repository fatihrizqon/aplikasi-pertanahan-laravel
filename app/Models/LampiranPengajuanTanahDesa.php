<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampiranPengajuanTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'lampiran_pengajuan_tanah_desa';

    protected $fillable = [
        'id_lampiran_jenis',
        'id_pengajuan_tanah_desa',
        'id_file',
    ];

    public function labels(): array
    {
        return [
            'id_lampiran_jenis'       => 'Jenis Lampiran',
            'id_pengajuan_tanah_desa' => 'Pengajuan Tanah Desa',
            'id_file'                 => 'File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_lampiran_jenis'       => ['required', 'integer', 'exists:lampiran_jenis,id'],
            'id_pengajuan_tanah_desa' => ['required', 'integer', 'exists:pengajuan_tanah_desa,id'],
            'id_file'                 => ['nullable', 'integer', 'exists:file,id'],
        ];
    }

    public function lampiranJenis(): BelongsTo
    {
        return $this->belongsTo(LampiranJenis::class, 'id_lampiran_jenis');
    }

    public function pengajuanTanahDesa(): BelongsTo
    {
        return $this->belongsTo(PengajuanTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }
}
