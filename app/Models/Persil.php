<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persil extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'persil';

    protected $fillable = [
        'id_kategori',
        'id_kelurahan',
        'jalan',
        'no_persil',
        'no_sertifikat',
        'luas',
        'batas_utara',
        'batas_selatan',
        'batas_timur',
        'batas_barat',
        'geom',
        'no_surat_ukur',
        'id_kategori_tanah_desa',
        'last_updated',
        'status_verifikasi',
        'id_user_verifikasi',
        'id_kategori_tanah_desa_detail',
    ];

    public function labels(): array
    {
        return [
            'id_kategori'                   => 'Kategori',
            'id_kelurahan'                  => 'Kelurahan',
            'jalan'                         => 'Jalan',
            'no_persil'                     => 'No. Persil',
            'no_sertifikat'                 => 'No. Sertifikat',
            'luas'                          => 'Luas (m²)',
            'batas_utara'                   => 'Batas Utara',
            'batas_selatan'                 => 'Batas Selatan',
            'batas_timur'                   => 'Batas Timur',
            'batas_barat'                   => 'Batas Barat',
            'geom'                          => 'Geometri',
            'no_surat_ukur'                 => 'No. Surat Ukur',
            'id_kategori_tanah_desa'        => 'Kategori Tanah Desa',
            'status_verifikasi'             => 'Status Verifikasi',
            'id_user_verifikasi'            => 'Diverifikasi Oleh',
            'id_kategori_tanah_desa_detail' => 'Detail Kategori Tanah Desa',
        ];
    }

    public function rules($scenario = null): array
    {
        $scenarios = [
            null => [
                'id_kategori'                   => ['nullable', 'integer', 'exists:kategori,id'],
                'id_kelurahan'                  => ['nullable', 'integer', 'exists:kelurahan,id'],
                'jalan'                         => ['nullable', 'string', 'max:128'],
                'no_persil'                     => ['required', 'string', 'max:32', Rule::unique($this->getTable())->ignore($this)],
                'no_sertifikat'                 => ['nullable', 'string', 'max:64'],
                'luas'                          => ['nullable', 'numeric'],
                'batas_utara'                   => ['nullable', 'string', 'max:256'],
                'batas_selatan'                 => ['nullable', 'string', 'max:256'],
                'batas_timur'                   => ['nullable', 'string', 'max:256'],
                'batas_barat'                   => ['nullable', 'string', 'max:256'],
                'geom'                          => ['nullable'],
                'no_surat_ukur'                 => ['nullable', 'string', 'max:16'],
                'id_kategori_tanah_desa'        => ['nullable', 'integer', 'exists:kategori_tanah_desa,id'],
                'status_verifikasi'             => ['nullable', 'integer'],
                'id_user_verifikasi'            => ['nullable', 'integer', 'exists:users,id'],
                'id_kategori_tanah_desa_detail' => ['nullable', 'integer', 'exists:kategori_tanah_desa_detail,id'],
            ],
            'update' => [
                'id_kategori'                   => ['nullable', 'integer', 'exists:kategori,id'],
                'id_kelurahan'                  => ['nullable', 'integer', 'exists:kelurahan,id'],
                'jalan'                         => ['nullable', 'string', 'max:128'],
                'no_persil'                     => ['required', 'string', 'max:32', Rule::unique($this->getTable())->ignore($this)],
                'no_sertifikat'                 => ['nullable', 'string', 'max:64'],
                'luas'                          => ['nullable', 'numeric'],
                'batas_utara'                   => ['nullable', 'string', 'max:256'],
                'batas_selatan'                 => ['nullable', 'string', 'max:256'],
                'batas_timur'                   => ['nullable', 'string', 'max:256'],
                'batas_barat'                   => ['nullable', 'string', 'max:256'],
                'geom'                          => ['nullable'],
                'no_surat_ukur'                 => ['nullable', 'string', 'max:16'],
                'id_kategori_tanah_desa'        => ['nullable', 'integer', 'exists:kategori_tanah_desa,id'],
                'status_verifikasi'             => ['nullable', 'integer'],
                'id_user_verifikasi'            => ['nullable', 'integer', 'exists:users,id'],
                'id_kategori_tanah_desa_detail' => ['nullable', 'integer', 'exists:kategori_tanah_desa_detail,id'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan');
    }

    public function kategoriTanahDesa(): BelongsTo
    {
        return $this->belongsTo(KategoriTanahDesa::class, 'id_kategori_tanah_desa');
    }

    public function kategoriTanahDesaDetail(): BelongsTo
    {
        return $this->belongsTo(KategoriTanahDesaDetail::class, 'id_kategori_tanah_desa_detail');
    }

    public function userVerifikasi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_verifikasi');
    }

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_persil');
    }

    public function monitorings(): HasMany
    {
        return $this->hasMany(Monitoring::class, 'id_persil');
    }
}
