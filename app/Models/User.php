<?php

namespace App\Models;

use App\Services\SmtpEmailService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'email', 'password', 'google_id', 'email_verified_at', 'two_factor_enabled', 'locked_until', 'last_login_ip', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'two_factor_enabled' => 'boolean',
            'locked_until'       => 'datetime',
            'last_login_at'      => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function roles()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function billingData()
    {
        return $this->hasMany(BillingData::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    public function sendEmailVerificationNotification(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        try {
            app(SmtpEmailService::class)->sendEmailVerification(
                $this->email,
                $this->name,
                $verificationUrl
            );
        } catch (\Throwable) {
            // Si SmtpEmailService falla, cae al sistema nativo de Laravel
            $this->notify(new VerifyEmail);
        }
    }

    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $this->email], false));

        try {
            app(SmtpEmailService::class)->sendPasswordReset($this->email, $this->name, $resetUrl);
        } catch (\Throwable) {
            $this->notify(new ResetPassword($token));
        }
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('role', $role)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isVendedor(): bool
    {
        return $this->hasRole('vendedor');
    }

    public function getHighestRole(): string
    {
        if ($this->isAdmin()) return 'admin';
        if ($this->isVendedor()) return 'vendedor';
        return 'usuario';
    }
}
