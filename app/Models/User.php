<?php

namespace App\Models;

use App\Notifications\VerifyAccountEmail;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'username',
        'status',
        'google_id',
        'facebook_id',
        'image',
        'onboarding',
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

    public function sendEmailVerificationNotification()
    {
        if (in_array($this->role, ['customer', 'seller'], true)) {
            $this->notify(new VerifyAccountEmail);
        }
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class, 'seller_id');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class, 'seller_id');
    }

    public function placeOrders()
    {
        return Order::where('user_id', auth()->id())->count();
    }
}
