<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'config';

    protected $fillable = [
        'id',
        'nama',
        'isi',
    ];

    public function labels(): array
    {
        return [
            'id'   => 'ID',
            'nama' => 'Nama',
            'isi'  => 'Isi',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'   => ['required', 'integer'],
            'nama' => ['required', 'string', 'max:32'],
            'isi'  => ['required', 'string', 'max:64'],
        ];
    }
}
