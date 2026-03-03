<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'coin',
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

// get profile picture url
    public function getProfilePictureUrlAttribute(): ?string
    {
        if (! $this->profile_picture) {
            return asset('images/default-avatar.jpg');;
        }

        return \Illuminate\Support\Facades\Storage::url($this->profile_picture);
    }

    public function sawitPlants():HasMany
    {
        return $this->hasMany((SawitPlant::class));
    }

    public function sawitTransactions(): HasMany
    {
        return $this->hasMany(SawitTransaction::class);
    }

    // legacy alias
    public function sawitPlantHistories():HasMany
    {
        return $this->sawitTransactions();
    }
}

