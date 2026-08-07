<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "users";

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'renter_rating' => 'decimal:2',
            'lender_rating' => 'decimal:2',
        ];
    }

    // Összes létrehozott eszközöm.
    public function gears(): HasMany
    {
        return $this->hasMany(Gear::class, 'user_id');
    }

    // Általam bérelt eszközök.
    public function rentalsAsRenter(): HasMany
    {
        return $this->hasMany(Rental::class, 'renter_id');
    }   

    // Bérbeadott eszközeim.
    public function rentalsAsOwner(): HasManyThrough
    {
        return $this->hasManyThrough(Renatl::class, Gear::class, 'user_id', 'gear_id');
    }

    // Kiadott értékelési rekordok.
    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    // Kapott értékelési rekordok. 
    public function ratingsReceieved(): HasMany
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }
}
