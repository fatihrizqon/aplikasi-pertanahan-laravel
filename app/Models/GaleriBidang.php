<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriBidang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'galeri_bidang';

    protected $fillable = [
        'id_bidang',
        'id_file',
        'nama',
    ];

    public function labels(): array
    {
        return [
            'id_bidang' => 'Bidang',
            'id_file'   => 'File',
            'nama'      => 'Nama',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_bidang' => ['nullable', 'integer', 'exists:bidang,id'],
            'id_file'   => ['nullable', 'integer', 'exists:file,id'],
            'nama'      => ['nullable', 'string', 'max:128'],
        ];
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }
}
