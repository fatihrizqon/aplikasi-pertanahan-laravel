<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampiranPengajuan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'lampiran_pengajuan';

    protected $fillable = [
        'id_lampiran_jenis',
        'id_pengajuan',
        'id_file',
    ];

    public function labels(): array
    {
        return [
            'id_lampiran_jenis' => 'Jenis Lampiran',
            'id_pengajuan'      => 'Pengajuan',
            'id_file'           => 'File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_lampiran_jenis' => ['required', 'integer', 'exists:lampiran_jenis,id'],
            'id_pengajuan'      => ['required', 'integer', 'exists:pengajuan,id'],
            'id_file'           => ['nullable', 'integer', 'exists:file,id'],
        ];
    }

    public function lampiranJenis(): BelongsTo
    {
        return $this->belongsTo(LampiranJenis::class, 'id_lampiran_jenis');
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }
}
