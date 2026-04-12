<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanTanahDesa extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'pengajuan_tanah_desa';

    protected $fillable = [
        'nomor',
        'tgl_masuk',
        'nama',
        'nama_instansi',
        'alamat',
        'id_tujuan_permohonan',
        'id_jenis_permohonan',
        'id_kondisi_lahan',
        'id_masa_berlaku',
        'id_kepemilikan_tanah',
        'lokasi',
        'id_kelurahan',
        'persil',
        'bidang',
        'sub_persil',
        'luas',
        'id_penggunaan',
        'keterangan',
        'tgl_mulai',
        'tgl_selesai',
        'longitude',
        'latitude',
        'diwakilkan',
        'nama_wakil',
        'alamat_wakil',
        'id_jenis_pengajuan',
        'id_status_pengajuan',
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
            'nomor'                 => 'Nomor',
            'tgl_masuk'             => 'Tanggal Masuk',
            'nama'                  => 'Nama Pemohon',
            'nama_instansi'         => 'Nama Instansi',
            'alamat'                => 'Alamat',
            'id_tujuan_permohonan'  => 'Tujuan Permohonan',
            'id_jenis_permohonan'   => 'Jenis Permohonan',
            'id_kondisi_lahan'      => 'Kondisi Lahan',
            'id_masa_berlaku'       => 'Masa Berlaku',
            'id_kepemilikan_tanah'  => 'Kepemilikan Tanah',
            'lokasi'                => 'Lokasi',
            'id_kelurahan'          => 'Kelurahan',
            'persil'                => 'Persil',
            'bidang'                => 'Bidang',
            'sub_persil'            => 'Sub Persil',
            'luas'                  => 'Luas (m²)',
            'id_penggunaan'         => 'Penggunaan',
            'keterangan'            => 'Keterangan',
            'tgl_mulai'             => 'Tanggal Mulai',
            'tgl_selesai'           => 'Tanggal Selesai',
            'longitude'             => 'Longitude',
            'latitude'              => 'Latitude',
            'diwakilkan'            => 'Diwakilkan',
            'nama_wakil'            => 'Nama Wakil',
            'alamat_wakil'          => 'Alamat Wakil',
            'id_jenis_pengajuan'    => 'Jenis Pengajuan',
            'id_status_pengajuan'   => 'Status Pengajuan',
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
                'id_tujuan_permohonan' => ['nullable', 'integer', 'exists:tujuan_permohonan,id'],
                'id_jenis_permohonan'  => ['nullable', 'integer', 'exists:jenis_permohonan,id'],
                'id_kondisi_lahan'     => ['nullable', 'integer', 'exists:kondisi_lahan,id'],
                'id_masa_berlaku'      => ['nullable', 'integer', 'exists:masa_berlaku,id'],
                'id_kepemilikan_tanah' => ['nullable', 'integer', 'exists:kepemilikan_tanah,id'],
                'lokasi'               => ['nullable', 'string', 'max:150'],
                'id_kelurahan'         => ['nullable', 'integer', 'exists:kelurahan,id'],
                'persil'               => ['nullable', 'string', 'max:64'],
                'bidang'               => ['nullable', 'string', 'max:20'],
                'sub_persil'           => ['nullable', 'string', 'max:20'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_tanah_desa,id'],
                'keterangan'           => ['nullable', 'string'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'longitude'            => ['nullable', 'string', 'max:255'],
                'latitude'             => ['nullable', 'string', 'max:255'],
                'diwakilkan'           => ['nullable', 'integer'],
                'nama_wakil'           => ['nullable', 'string', 'max:20'],
                'alamat_wakil'         => ['nullable', 'string'],
                'id_jenis_pengajuan'   => ['nullable', 'integer', 'exists:jenis_pengajuan,id'],
                'id_status_pengajuan'  => ['nullable', 'integer', 'exists:status_pengajuan,id'],
            ],
            'update' => [
                'nomor'                => ['required', 'string', 'max:20', Rule::unique($this->getTable())->ignore($this)],
                'tgl_masuk'            => ['nullable', 'date'],
                'nama'                 => ['nullable', 'string', 'max:100'],
                'nama_instansi'        => ['nullable', 'string', 'max:100'],
                'alamat'               => ['nullable', 'string'],
                'id_tujuan_permohonan' => ['nullable', 'integer', 'exists:tujuan_permohonan,id'],
                'id_jenis_permohonan'  => ['nullable', 'integer', 'exists:jenis_permohonan,id'],
                'id_kondisi_lahan'     => ['nullable', 'integer', 'exists:kondisi_lahan,id'],
                'id_masa_berlaku'      => ['nullable', 'integer', 'exists:masa_berlaku,id'],
                'id_kepemilikan_tanah' => ['nullable', 'integer', 'exists:kepemilikan_tanah,id'],
                'lokasi'               => ['nullable', 'string', 'max:150'],
                'id_kelurahan'         => ['nullable', 'integer', 'exists:kelurahan,id'],
                'persil'               => ['nullable', 'string', 'max:64'],
                'bidang'               => ['nullable', 'string', 'max:20'],
                'sub_persil'           => ['nullable', 'string', 'max:20'],
                'luas'                 => ['nullable', 'numeric'],
                'id_penggunaan'        => ['nullable', 'integer', 'exists:penggunaan_tanah_desa,id'],
                'keterangan'           => ['nullable', 'string'],
                'tgl_mulai'            => ['nullable', 'date'],
                'tgl_selesai'          => ['nullable', 'date'],
                'longitude'            => ['nullable', 'string', 'max:255'],
                'latitude'             => ['nullable', 'string', 'max:255'],
                'diwakilkan'           => ['nullable', 'integer'],
                'nama_wakil'           => ['nullable', 'string', 'max:20'],
                'alamat_wakil'         => ['nullable', 'string'],
                'id_jenis_pengajuan'   => ['nullable', 'integer', 'exists:jenis_pengajuan,id'],
                'id_status_pengajuan'  => ['nullable', 'integer', 'exists:status_pengajuan,id'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }

    public function tujuanPermohonan(): BelongsTo
    {
        return $this->belongsTo(TujuanPermohonan::class, 'id_tujuan_permohonan');
    }

    public function jenisPermohonan(): BelongsTo
    {
        return $this->belongsTo(JenisPermohonan::class, 'id_jenis_permohonan');
    }

    public function kondisiLahan(): BelongsTo
    {
        return $this->belongsTo(KondisiLahan::class, 'id_kondisi_lahan');
    }

    public function masaBerlaku(): BelongsTo
    {
        return $this->belongsTo(MasaBerlaku::class, 'id_masa_berlaku');
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
        return $this->belongsTo(PenggunaanTanahDesa::class, 'id_penggunaan');
    }

    public function jenisPengajuan(): BelongsTo
    {
        return $this->belongsTo(JenisPengajuan::class, 'id_jenis_pengajuan');
    }

    public function statusPengajuan(): BelongsTo
    {
        return $this->belongsTo(StatusPengajuan::class, 'id_status_pengajuan');
    }

    public function verifikasis(): HasMany
    {
        return $this->hasMany(VerifikasiTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function rekomendasies(): HasMany
    {
        return $this->hasMany(RekomendasiTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function persetujuanKadipatens(): HasMany
    {
        return $this->hasMany(PersetujuanKadipatanTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function skGubernurs(): HasMany
    {
        return $this->hasMany(SkGubernurTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function lampiranPengajuans(): HasMany
    {
        return $this->hasMany(LampiranPengajuanTanahDesa::class, 'id_pengajuan_tanah_desa');
    }

    public function persilTanahDesas(): HasMany
    {
        return $this->hasMany(PersilTanahDesa::class, 'id_pengajuan_tanah_desa');
    }
}
