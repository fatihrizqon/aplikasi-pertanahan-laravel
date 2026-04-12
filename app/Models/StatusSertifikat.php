<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusSertifikat extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'status_sertifikat';

    protected $fillable = [
        'nama',
        'warna',
        'ontop',
    ];

    public function labels(): array
    {
        return [
            'nama'  => 'Nama',
            'warna' => 'Warna',
            'ontop' => 'On Top',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama'  => ['nullable', 'string', 'max:256'],
            'warna' => ['nullable', 'string', 'max:15'],
            'ontop' => ['nullable', 'integer'],
        ];
    }

    public function bidangs(): HasMany
    {
        return $this->hasMany(Bidang::class, 'id_status_sertifikat');
    }
}
