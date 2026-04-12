<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class JenisMonitoring extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'jenis_monitoring';

    protected $fillable = [
        'id',
        'nama',
    ];

    public function labels(): array
    {
        return [
            'id'   => 'ID',
            'nama' => 'Nama',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'   => ['required', 'integer'],
            'nama' => ['required', 'string', 'max:32'],
        ];
    }
}
