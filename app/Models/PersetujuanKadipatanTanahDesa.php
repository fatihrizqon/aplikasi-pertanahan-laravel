<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanKadipatanTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'persetujuan_kadipaten_tanah_desa';

    protected $fillable = [
        'id_pengajuan_tanah_desa',
        'status',
        'no_surat',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'id_file',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai'   => 'date',
            'tgl_selesai' => 'date',
        ];
    }

    public function labels(): array
    {
        return [
            'id_pengajuan_tanah_desa' => 'Pengajuan Tanah Desa',
            'status'                  => 'Status',
            'no_surat'                => 'No. Surat',
            'tgl_mulai'               => 'Tanggal Mulai',
            'tgl_selesai'             => 'Tanggal Selesai',
            'keterangan'              => 'Keterangan',
            'id_file'                 => 'File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan_tanah_desa' => ['required', 'integer', 'exists:pengajuan_tanah_desa,id'],
            'status'                  => ['required', 'integer'],
            'no_surat'                => ['nullable', 'string', 'max:50'],
            'tgl_mulai'               => ['nullable', 'date'],
            'tgl_selesai'             => ['nullable', 'date'],
            'keterangan'              => ['nullable', 'string', 'max:225'],
            'id_file'                 => ['nullable', 'integer', 'exists:file,id'],
        ];
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
