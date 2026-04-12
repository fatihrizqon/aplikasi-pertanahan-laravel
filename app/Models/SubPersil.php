<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubPersil extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'sub_persil';

    protected $fillable = [
        'id_bidang',
        'no_sub_persil',
        'no_serat_kekancingan',
        'tgl_mulai',
        'tgl_selesai',
        'luas',
        'id_penggunaan',
        'id_pengelola',
        'keterangan',
        'id_file',
        'last_updated',
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
            'id_bidang'            => 'Bidang',
            'no_sub_persil'        => 'No. Sub Persil',
            'no_serat_kekancingan' => 'No. Serat Kekancingan',
            'tgl_mulai'            => 'Tanggal Mulai',
            'tgl_selesai'          => 'Tanggal Selesai',
            'luas'                 => 'Luas (m²)',
            'id_penggunaan'        => 'Penggunaan',
            'id_pengelola'         => 'Pengelola',
            'keterangan'           => 'Keterangan',
            'id_file'              => 'File Dokumen',
        ];
    }

    public function rules($scenario = null): array
    {
        $scenarios = [
            null => [
                'id_bidang'            => ['required', 'integer', 'exists:bidang,id'],
                'no_sub_persil'        => ['nullable', 'string', 'max:64'],
                'no_serat_kekancingan' => ['nullable', 'string', 'max:64'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_rtr,id'],
                'id_pengelola'         => ['nullable', 'integer', 'exists:pengelola,id'],
                'keterangan'           => ['nullable', 'string', 'max:512'],
                'id_file'              => ['nullable', 'integer', 'exists:file,id'],
                'last_updated'         => ['nullable', 'string', 'max:500'],
            ],
            'update' => [
                'id_bidang'            => ['required', 'integer', 'exists:bidang,id'],
                'no_sub_persil'        => ['nullable', 'string', 'max:64'],
                'no_serat_kekancingan' => ['nullable', 'string', 'max:64'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_rtr,id'],
                'id_pengelola'         => ['nullable', 'integer', 'exists:pengelola,id'],
                'keterangan'           => ['nullable', 'string', 'max:512'],
                'id_file'              => ['nullable', 'integer', 'exists:file,id'],
                'last_updated'         => ['nullable', 'string', 'max:500'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }

    public function penggunaan(): BelongsTo
    {
        return $this->belongsTo(PenggunaanRtr::class, 'id_penggunaan');
    }

    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(Pengelola::class, 'id_pengelola');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }

    public function galeris(): HasMany
    {
        return $this->hasMany(GaleriSubPersil::class, 'id_sub_persil');
    }
}
