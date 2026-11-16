<?php

namespace App\Models;

use App\Modules\RegistrationModule\Models\Registration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi One-to-Many: 1 User bisa memiliki banyak Registration
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}