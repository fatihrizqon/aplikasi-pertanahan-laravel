<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'pengajuan';

    protected $fillable = [
        'nomor',
        'tgl_masuk',
        'nama',
        'nama_instansi',
        'alamat',
        'id_jenis_permohonan',
        'id_kepemilikan_tanah',
        'lokasi',
        'id_kelurahan',
        'persil',
        'bidang',
        'sub_persil',
        'luas',
        'id_penggunaan',
        'diwakilkan',
        'nama_wakil',
        'alamat_wakil',
        'tgl_mulai',
        'tgl_selesai',
        'id_status_pengajuan',
        'id_jenis_pengajuan',
        'no_kekancingan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_masuk'   => 'date',
            'tgl_mulai'   => 'date',
            'tgl_selesai' => 'date',
        ];
    }

    public function labels(): array
    {
        return [
            'nomor'                => 'Nomor',
            'tgl_masuk'            => 'Tanggal Masuk',
            'nama'                 => 'Nama Pemohon',
            'nama_instansi'        => 'Nama Instansi',
            'alamat'               => 'Alamat',
            'id_jenis_permohonan'  => 'Jenis Permohonan',
            'id_kepemilikan_tanah' => 'Kepemilikan Tanah',
            'lokasi'               => 'Lokasi',
            'id_kelurahan'         => 'Kelurahan',
            'persil'               => 'Persil',
            'bidang'               => 'Bidang',
            'sub_persil'           => 'Sub Persil',
            'luas'                 => 'Luas (m²)',
            'id_penggunaan'        => 'Penggunaan',
            'diwakilkan'           => 'Diwakilkan',
            'nama_wakil'           => 'Nama Wakil',
            'alamat_wakil'         => 'Alamat Wakil',
            'tgl_mulai'            => 'Tanggal Mulai',
            'tgl_selesai'          => 'Tanggal Selesai',
            'id_status_pengajuan'  => 'Status Pengajuan',
            'id_jenis_pengajuan'   => 'Jenis Pengajuan',
            'no_kekancingan'       => 'No. Kekancingan',
            'keterangan'           => 'Keterangan',
        ];
    }

    public function rules($scenario = null): array
    {
        $scenarios = [
            null => [
                'nomor'                => ['required', 'string', 'max:20', Rule::unique($this->getTable())->ignore($this)],
                'tgl_masuk'            => ['nullable', 'date'],
                'nama'                 => ['nullable', 'string', 'max:100'],
                'nama_instansi'        => ['nullable', 'string', 'max:100'],
                'alamat'               => ['nullable', 'string'],
                'id_jenis_permohonan'  => ['nullable', 'integer', 'exists:jenis_permohonan,id'],
                'id_kepemilikan_tanah' => ['nullable', 'integer', 'exists:kepemilikan_tanah,id'],
                'lokasi'               => ['nullable', 'string', 'max:150'],
                'id_kelurahan'         => ['nullable', 'integer', 'exists:kelurahan,id'],
                'persil'               => ['nullable', 'string', 'max:64'],
                'bidang'               => ['nullable', 'string', 'max:20'],
                'sub_persil'           => ['nullable', 'string', 'max:20'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_rtr,id'],
                'diwakilkan'           => ['nullable', 'integer'],
                'nama_wakil'           => ['nullable', 'string', 'max:20'],
                'alamat_wakil'         => ['nullable', 'string'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'id_status_pengajuan'  => ['nullable', 'integer', 'exists:status_pengajuan,id'],
                'id_jenis_pengajuan'   => ['nullable', 'integer', 'exists:jenis_pengajuan,id'],
                'no_kekancingan'       => ['nullable', 'string', 'max:64'],
                'keterangan'           => ['nullable', 'string', 'max:512'],
            ],
            'update' => [
                'nomor'                => ['required', 'string', 'max:20', Rule::unique($this->getTable())->ignore($this)],
                'tgl_masuk'            => ['nullable', 'date'],
                'nama'                 => ['nullable', 'string', 'max:100'],
                'nama_instansi'        => ['nullable', 'string', 'max:100'],
                'alamat'               => ['nullable', 'string'],
                'id_jenis_permohonan'  => ['nullable', 'integer', 'exists:jenis_permohonan,id'],
                'id_kepemilikan_tanah' => ['nullable', 'integer', 'exists:kepemilikan_tanah,id'],
                'lokasi'               => ['nullable', 'string', 'max:150'],
                'id_kelurahan'         => ['nullable', 'integer', 'exists:kelurahan,id'],
                'persil'               => ['nullable', 'string', 'max:64'],
                'bidang'               => ['nullable', 'string', 'max:20'],
                'sub_persil'           => ['nullable', 'string', 'max:20'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_rtr,id'],
                'diwakilkan'           => ['nullable', 'integer'],
                'nama_wakil'           => ['nullable', 'string', 'max:20'],
                'alamat_wakil'         => ['nullable', 'string'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'id_status_pengajuan'  => ['nullable', 'integer', 'exists:status_pengajuan,id'],
                'id_jenis_pengajuan'   => ['nullable', 'integer', 'exists:jenis_pengajuan,id'],
                'no_kekancingan'       => ['nullable', 'string', 'max:64'],
                'keterangan'           => ['nullable', 'string', 'max:512'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }

    public function jenisPengajuan(): BelongsTo
    {
        return $this->belongsTo(JenisPengajuan::class, 'id_jenis_pengajuan');
    }

    public function jenisPermohonan(): BelongsTo
    {
        return $this->belongsTo(JenisPermohonan::class, 'id_jenis_permohonan');
    }

    public function kepemilikanTanah(): BelongsTo
    {
        return $this->belongsTo(KepemilikanTanah::class, 'id_kepemilikan_tanah');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan');
    }

    public function penggunaan(): BelongsTo
    {
        return $this->belongsTo(PenggunaanRtr::class, 'id_penggunaan');
    }

    public function statusPengajuan(): BelongsTo
    {
        return $this->belongsTo(StatusPengajuan::class, 'id_status_pengajuan');
    }

    public function verifikasis(): HasMany
    {
        return $this->hasMany(Verifikasi::class, 'id_pengajuan');
    }

    public function rekomendasies(): HasMany
    {
        return $this->hasMany(Rekomendasi::class, 'id_pengajuan');
    }

    public function persetujuanKadipatens(): HasMany
    {
        return $this->hasMany(PersetujuanKadipaten::class, 'id_pengajuan');
    }

    public function skGubernurs(): HasMany
    {
        return $this->hasMany(SkGubernur::class, 'id_pengajuan');
    }

    public function lampiranPengajuans(): HasMany
    {
        return $this->hasMany(LampiranPengajuan::class, 'id_pengajuan');
    }
}
