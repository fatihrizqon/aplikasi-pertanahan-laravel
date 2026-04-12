<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Monitoring extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'monitoring';

    protected $fillable = [
        'id_jenis_monitoring',
        'hasil',
        'id_file',
        'id_file_pendukung',
        'tanggal',
        'id_persil',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function labels(): array
    {
        return [
            'id_jenis_monitoring' => 'Jenis Monitoring',
            'hasil'               => 'Hasil',
            'id_file'             => 'File Laporan',
            'id_file_pendukung'   => 'File Pendukung',
            'tanggal'             => 'Tanggal',
            'id_persil'           => 'Persil',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_jenis_monitoring' => ['nullable', 'integer'],
            'hasil'               => ['nullable', 'string'],
            'id_file'             => ['nullable', 'integer', 'exists:file,id'],
            'id_file_pendukung'   => ['nullable', 'integer', 'exists:file,id'],
            'tanggal'             => ['nullable', 'date'],
            'id_persil'           => ['nullable', 'integer', 'exists:persil,id'],
        ];
    }

    public function persil(): BelongsTo
    {
        return $this->belongsTo(Persil::class, 'id_persil');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }

    public function filePendukung(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file_pendukung');
    }
}
