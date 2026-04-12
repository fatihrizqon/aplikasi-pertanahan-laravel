<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ModelTrait;
use Laravel\Scout\Searchable;
use Illuminate\Validation\Rule;
use App\Traits\ValidatableTrait;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Searchable, ModelTrait, ValidatableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function labels(): array
    {
        return [
            'username' => 'Username',
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Kata Sandi',
            'status' => 'Status',
            'created_by' => 'Dibuat Oleh',
        ];
    }

    public function rules($scenario = null)
    {
        $scenarios = [
            null => [
                'username' => ['required', 'lowercase', Rule::unique($this->getTable())->ignore($this)],
                'name' => ['required'],
                'email' => ['required', 'lowercase', Rule::unique($this->getTable())->ignore($this)],
                'password' => ['required', 'min:5', 'confirmed'],
                'password_confirmation' => ['required'],
                'status' => ['nullable'],
                'created_by' => ['nullable'],
            ],
            'update' => [
                'username' => ['required', 'lowercase', Rule::unique($this->getTable())->ignore($this)],
                'name' => ['required'],
                'email' => ['required', 'lowercase', Rule::unique($this->getTable())->ignore($this)],
                'password' => ['nullable', 'min:5', 'confirmed'],
                'password_confirmation' => ['nullable'],
                'status' => ['nullable'],
                'created_by' => ['nullable'],
            ],
            'update-profile' => [
                'username' => ['required', 'lowercase', Rule::unique($this->getTable())->ignore($this)],
                'name' => ['required'],
                'email' => ['required', 'lowercase', Rule::unique($this->getTable())->ignore($this)],
                'current_password' => [
                    'nullable',
                    'required_with:password',
                    function ($attribute, $value, $fail) {
                        if (request()->filled('password')) {
                            if (! Hash::check($value, request()->user()->password)) {
                                $fail('Current password is incorrect.');
                            }
                        }
                    },
                ],
                'password' => ['nullable', 'min:5', 'confirmed'],
                'password_confirmation' => ['nullable', 'required_with:password'],
                'status' => ['nullable'],
                'created_by' => ['nullable'],
            ],
        ];

        return $scenarios[$scenario] ?? $scenarios[null];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('superadmin')) {
            return $query;
        }

        if ($user->hasRole('admin')) {
            return $query->where('created_by', $user->id);
        }

        return $query->whereKey(null);
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public static function excelColumns(): array
    {
        return [
            'Username' => 'username',
            'Name'     => 'name',
            'Email'    => 'email',
            'Status'   => 'status',
        ];
    }

    public static function excelHeadings(): array
    {
        return array_keys(self::excelColumns());
    }

    public static function excelMapFromModel(self $user): array
    {
        return collect(self::excelColumns())
               ->map(fn ($attr) => data_get($user, $attr))
               ->values()
               ->toArray();
    }

    public static function excelMapFromRow(array $row): array
    {
        return collect(self::excelColumns())
               ->mapWithKeys(function ($attr, $header) use ($row) {
                   return [$attr => $row[strtolower($header)] ?? null];
               })
               ->toArray();
    }
}
