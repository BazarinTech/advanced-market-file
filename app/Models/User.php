<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'phone',
        'passwrd',
        'password',
        'status',
        'refer',
        'invite_code',
        'country',
        'role',
    ];

    protected $hidden = [
        'passwrd',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'          => 'hashed',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function earnings()
    {
        return $this->hasOne(Earning::class, 'email', 'email');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'email', 'email');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'email', 'email');
    }

    public function downline()
    {
        return $this->hasMany(User::class, 'refer', 'ID');
    }

    public static function generateInviteCode(): string
    {
        do {
            $code = (string) random_int(100000, 999999);
        } while (static::where('invite_code', $code)->exists());

        return $code;
    }
}
