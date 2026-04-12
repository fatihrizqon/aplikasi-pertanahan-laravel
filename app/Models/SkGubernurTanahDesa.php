<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkGubernurTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'sk_gubernur_tanah_desa';

    protected $fillable = [
        'id_pengajuan_tanah_desa',
        'no_sk',
        'id_file',
    ];

    public function labels(): array
    {
        return [
            'id_pengajuan_tanah_desa' => 'Pengajuan Tanah Desa',
            'no_sk'                   => 'No. SK',
            'id_file'                 => 'File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan_tanah_desa' => ['required', 'integer', 'exists:pengajuan_tanah_desa,id'],
            'no_sk'                   => ['nullable', 'string', 'max:50'],
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
