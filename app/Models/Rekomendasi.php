<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rekomendasi extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'rekomendasi';

    protected $fillable = [
        'id_pengajuan',
        'no_surat',
        'keterangan',
        'id_file',
    ];

    public function labels(): array
    {
        return [
            'id_pengajuan' => 'Pengajuan',
            'no_surat'     => 'No. Surat',
            'keterangan'   => 'Keterangan',
            'id_file'      => 'File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan' => ['required', 'integer', 'exists:pengajuan,id'],
            'no_surat'     => ['required', 'string', 'max:50'],
            'keterangan'   => ['required', 'string', 'max:225'],
            'id_file'      => ['nullable', 'integer', 'exists:file,id'],
        ];
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
