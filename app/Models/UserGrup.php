<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGrup extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'user_grup';

    protected $fillable = [
        'id',
        'nama',
    ];

    public function labels(): array
    {
        return [
            'id'   => 'ID',
            'nama' => 'Nama Grup',
        ];
    }

    public function rules($scenario = null): array
    {
        return [
            'id'   => ['required', 'integer'],
            'nama' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_grup');
    }
}
