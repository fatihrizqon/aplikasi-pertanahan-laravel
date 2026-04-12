<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Clickbar\Magellan\Data\Geometries\Geometry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'bidang';

    protected $casts = [
        'geom' => Geometry::class,
    ];

    protected $fillable = [
        'id_jenis_hak',
        'id_jenis_uupa',
        'no_surat_uupa',
        'no_bidang',
        'id_pengelola',
        'no_kekancingan',
        'luas',
        'id_penggunaan',
        'tgl_mulai',
        'tgl_selesai',
        'keterangan',
        'id_status_kesesuaian',
        'no_sertifikat',
        'id_file',
        'id_status_sertifikat',
        'geom',
        'id_persil',
        'id_kesesuaian_rdtr',
        'id_peta',
        'id_sg_pag_lama',
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
            'id_jenis_hak'          => 'Jenis Hak',
            'id_jenis_uupa'         => 'Jenis UUPA',
            'no_surat_uupa'         => 'No. Surat UUPA',
            'no_bidang'             => 'No. Bidang',
            'id_pengelola'          => 'Pengelola',
            'no_kekancingan'        => 'No. Kekancingan',
            'luas'                  => 'Luas (m²)',
            'id_penggunaan'         => 'Penggunaan',
            'tgl_mulai'             => 'Tanggal Mulai',
            'tgl_selesai'           => 'Tanggal Selesai',
            'keterangan'            => 'Keterangan',
            'id_status_kesesuaian'  => 'Status Kesesuaian',
            'no_sertifikat'         => 'No. Sertifikat',
            'id_file'               => 'File Dokumen',
            'id_status_sertifikat'  => 'Status Sertifikat',
            'geom'                  => 'Geometri',
            'id_persil'             => 'Persil',
            'id_kesesuaian_rdtr'    => 'Kesesuaian RDTR',
            'id_peta'               => 'File Peta',
            'id_sg_pag_lama'        => 'ID SG/PAG Lama',
            'last_updated'          => 'Terakhir Diperbarui',
        ];
    }

    public function rules($scenario = null): array
    {
        $scenarios = [
            null => [
                'id_jenis_hak'         => ['nullable', 'integer', 'exists:jenis_hak,id'],
                'id_jenis_uupa'        => ['nullable', 'integer', 'exists:jenis_uupa,id'],
                'no_surat_uupa'        => ['nullable', 'string', 'max:64'],
                'no_bidang'            => ['required', 'string', 'max:64'],
                'id_pengelola'         => ['nullable', 'integer', 'exists:pengelola,id'],
                'no_kekancingan'       => ['nullable', 'string', 'max:64'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_rtr,id'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'keterangan'           => ['nullable', 'string', 'max:512'],
                'id_status_kesesuaian' => ['nullable', 'integer', 'exists:status_kesesuaian,id'],
                'no_sertifikat'        => ['nullable', 'string', 'max:128'],
                'id_file'              => ['nullable', 'integer', 'exists:file,id'],
                'id_status_sertifikat' => ['nullable', 'integer', 'exists:status_sertifikat,id'],
                'geom'                 => ['nullable'],
                'id_persil'            => ['nullable', 'integer', 'exists:persil,id'],
                'id_kesesuaian_rdtr'   => ['nullable', 'integer', 'exists:status_kesesuaian,id'],
                'id_peta'              => ['nullable', 'integer', 'exists:file,id'],
                'id_sg_pag_lama'       => ['nullable', 'string', 'max:256'],
                'last_updated'         => ['nullable', 'string', 'max:500'],
            ],
            'update' => [
                'id_jenis_hak'         => ['nullable', 'integer', 'exists:jenis_hak,id'],
                'id_jenis_uupa'        => ['nullable', 'integer', 'exists:jenis_uupa,id'],
                'no_surat_uupa'        => ['nullable', 'string', 'max:64'],
                'no_bidang'            => ['required', 'string', 'max:64'],
                'id_pengelola'         => ['nullable', 'integer', 'exists:pengelola,id'],
                'no_kekancingan'       => ['nullable', 'string', 'max:64'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_rtr,id'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'keterangan'           => ['nullable', 'string', 'max:512'],
                'id_status_kesesuaian' => ['nullable', 'integer', 'exists:status_kesesuaian,id'],
                'no_sertifikat'        => ['nullable', 'string', 'max:128'],
                'id_file'              => ['nullable', 'integer', 'exists:file,id'],
                'id_status_sertifikat' => ['nullable', 'integer', 'exists:status_sertifikat,id'],
                'geom'                 => ['nullable'],
                'id_persil'            => ['nullable', 'integer', 'exists:persil,id'],
                'id_kesesuaian_rdtr'   => ['nullable', 'integer', 'exists:status_kesesuaian,id'],
                'id_peta'              => ['nullable', 'integer', 'exists:file,id'],
                'id_sg_pag_lama'       => ['nullable', 'string', 'max:256'],
                'last_updated'         => ['nullable', 'string', 'max:500'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }

    public function persil(): BelongsTo
    {
        return $this->belongsTo(Persil::class, 'id_persil');
    }

    public function jenisHak(): BelongsTo
    {
        return $this->belongsTo(JenisHak::class, 'id_jenis_hak');
    }

    public function jenisUupa(): BelongsTo
    {
        return $this->belongsTo(JenisUupa::class, 'id_jenis_uupa');
    }

    public function pengelola(): BelongsTo
    {
        return $this->belongsTo(Pengelola::class, 'id_pengelola');
    }

    public function penggunaan(): BelongsTo
    {
        return $this->belongsTo(PenggunaanRtr::class, 'id_penggunaan');
    }

    public function statusKesesuaian(): BelongsTo
    {
        return $this->belongsTo(StatusKesesuaian::class, 'id_status_kesesuaian');
    }

    public function kesesuaianRdtr(): BelongsTo
    {
        return $this->belongsTo(StatusKesesuaian::class, 'id_kesesuaian_rdtr');
    }

    public function statusSertifikat(): BelongsTo
    {
        return $this->belongsTo(StatusSertifikat::class, 'id_status_sertifikat');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_file');
    }

    public function peta(): BelongsTo
    {
        return $this->belongsTo(File::class, 'id_peta');
    }

    public function subPersils(): HasMany
    {
        return $this->hasMany(SubPersil::class, 'id_bidang');
    }

    public function galeris(): HasMany
    {
        return $this->hasMany(GaleriBidang::class, 'id_bidang');
    }
}
