<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'category', 'attributes', 'price_per_day', 'city', 'address'])]
class Gear extends Model
{
    protected $table = "gears";

    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(GearImage::class, 'gear_id');
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'gear_id');
    }
}
