<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'file';

    protected $fillable = [
        'nama',
    ];

    public function labels(): array
    {
        return [
            'nama' => 'Nama File',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'nama' => ['nullable', 'string', 'max:256'],
        ];
    }
}
