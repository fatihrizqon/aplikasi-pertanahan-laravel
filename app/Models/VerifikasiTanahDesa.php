<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerifikasiTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'verifikasi_tanah_desa';

    protected $fillable = [
        'id_pengajuan_tanah_desa',
        'id_user',
        'status',
        'alasan',
    ];

    public function labels(): array
    {
        return [
            'id_pengajuan_tanah_desa' => 'Pengajuan Tanah Desa',
            'id_user'                 => 'Petugas',
            'status'                  => 'Status',
            'alasan'                  => 'Alasan',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan_tanah_desa' => ['required', 'integer', 'exists:pengajuan_tanah_desa,id'],
            'id_user'                 => ['nullable', 'integer', 'exists:users,id'],
            'status'                  => ['required', 'integer'],
            'alasan'                  => ['required', 'string', 'max:225'],
        ];
    }

    public function pengajuanTanahDesa(): BelongsTo
    {
        return $this->belongsTo(PengajuanTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
