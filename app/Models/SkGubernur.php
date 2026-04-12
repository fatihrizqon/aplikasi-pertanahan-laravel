<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkGubernur extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'sk_gubernur';

    protected $fillable = [
        'id_pengajuan',
        'no_sk',
        'id_file',
    ];

    public function labels(): array
    {
        return [
            'id_pengajuan' => 'Pengajuan',
            'no_sk'        => 'No. SK',
            'id_file'      => 'File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_pengajuan' => ['required', 'integer', 'exists:pengajuan,id'],
            'no_sk'        => ['nullable', 'string', 'max:50'],
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
