<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class TanggalNotifikasi extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'tanggal_notifikasi';

    protected $fillable = [
        'tanggal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function labels(): array
    {
        return [
            'tanggal'    => 'Tanggal',
            'keterangan' => 'Keterangan',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'tanggal'    => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:25'],
        ];
    }
}
