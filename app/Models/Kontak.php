<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\ValidatableTrait;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use ModelTrait, ValidatableTrait;

    public $timestamps = false;

    protected $table = 'kontak';

    protected $fillable = [
        'tanggal',
        'nama',
        'email',
        'subyek',
        'pesan',
        'balasan',
        'status',
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
            'tanggal' => 'Tanggal',
            'nama'    => 'Nama',
            'email'   => 'Email',
            'subyek'  => 'Subyek',
            'pesan'   => 'Pesan',
            'balasan' => 'Balasan',
            'status'  => 'Status',
        ];
    }

    public function rules($scenario = null): array
    {
        $scenarios = [
            null => [
                'tanggal' => ['nullable', 'date'],
                'nama'    => ['required', 'string', 'max:128'],
                'email'   => ['required', 'email', 'max:32'],
                'subyek'  => ['required', 'string', 'max:32'],
                'pesan'   => ['nullable', 'string', 'max:255'],
                'balasan' => ['nullable', 'string', 'max:255'],
                'status'  => ['nullable', 'integer'],
            ],
            'balas' => [
                'balasan' => ['required', 'string', 'max:255'],
                'status'  => ['nullable', 'integer'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }
}
