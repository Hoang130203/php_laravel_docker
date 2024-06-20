<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens,TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
    public function setTwoFactorCode($code)
    {
        $this->two_factor_code = $code;
        $this->two_factor_code_expiry=now()->addMinutes(10);
        $this->save();
    }

    public function getTwoFactorCode()
    {
        return $this->two_factor_code;
    }

    public function resetTwoFactorCode()
    {
        $this->two_factor_code= null;
        $this->two_factor_code_expiry=null;
        $this->save();
    }

    public function isTwoFactorCodeExpired()
    {
        return $this->two_factor_code_expiry && $this->two_factor_code_expiry->isPast();
    }

}
