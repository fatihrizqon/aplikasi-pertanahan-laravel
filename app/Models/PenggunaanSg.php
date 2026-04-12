<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenggunaanSg extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'penggunaan_sg';

    protected $fillable = [
        'id_penggunaan',
        'nama',
    ];

    public function labels(): array
    {
        return [
            'id_penggunaan' => 'Penggunaan RTR',
            'nama'          => 'Nama',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id_penggunaan' => ['required', 'integer', 'exists:penggunaan_rtr,id'],
            'nama'          => ['required', 'string', 'max:255'],
        ];
    }

    public function penggunaanRtr(): BelongsTo
    {
        return $this->belongsTo(PenggunaanRtr::class, 'id_penggunaan');
    }
}
