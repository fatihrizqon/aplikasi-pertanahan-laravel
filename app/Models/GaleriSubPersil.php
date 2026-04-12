<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaleriSubPersil extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'galeri_sub_persil';

    protected $fillable = [
        'id_file',
        'id_sub_persil',
        'nama',
    ];

    public function labels(): array
    {
        return [
            'id_file'       => 'File',
            'id_sub_persil' => 'Sub Persil',
            'nama'          => 'Nama',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_file'       => ['nullable', 'integer', 'exists:file,id'],
            'id_sub_persil' => ['nullable', 'integer', 'exists:sub_persil,id'],
            'nama'          => ['nullable', 'string', 'max:255'],
        ];
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }

    public function subPersil(): BelongsTo
    {
        return $this->belongsTo(SubPersil::class, 'id_sub_persil');
    }
}
