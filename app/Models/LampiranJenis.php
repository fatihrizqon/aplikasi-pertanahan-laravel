<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LampiranJenis extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'lampiran_jenis';

    protected $fillable = [
        'id_lampiran_kategori',
        'nama',
        'hint',
    ];

    public function labels(): array
    {
        return [
            'id_lampiran_kategori' => 'Kategori Lampiran',
            'nama'                 => 'Nama',
            'hint'                 => 'Hint',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_lampiran_kategori' => ['required', 'integer', 'exists:lampiran_kategori,id'],
            'nama'                 => ['required', 'string', 'max:100'],
            'hint'                 => ['nullable', 'string', 'max:100'],
        ];
    }

    public function lampiranKategori(): BelongsTo
    {
        return $this->belongsTo(LampiranKategori::class, 'id_lampiran_kategori');
    }

    public function lampiranPengajuans(): HasMany
    {
        return $this->hasMany(LampiranPengajuan::class, 'id_lampiran_jenis');
    }

    public function lampiranPengajuanTanahDesas(): HasMany
    {
        return $this->hasMany(LampiranPengajuanTanahDesa::class, 'id_lampiran_jenis');
    }

    public function lampiranJenisTanahDesas(): HasMany
    {
        return $this->hasMany(LampiranJenisTanahDesa::class, 'id_lampiran_jenis');
    }
}
