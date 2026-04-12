<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verifikasi extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'verifikasi';

    protected $fillable = [
        'id_pengajuan',
        'id_user',
        'status',
        'alasan',
    ];

    public function labels(): array
    {
        return [
            'id_pengajuan' => 'Pengajuan',
            'id_user'      => 'Petugas',
            'status'       => 'Status',
            'alasan'       => 'Alasan',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan' => ['required', 'integer', 'exists:pengajuan,id'],
            'id_user'      => ['nullable', 'integer', 'exists:users,id'],
            'status'       => ['required', 'integer'],
            'alasan'       => ['required', 'string', 'max:225'],
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
