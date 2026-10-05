<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['score', 'comment'])]
class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_it');
    }

    public function rated():BelongsTo {
        return $this->belongsTo(User::class, 'rated_id');
    }
}
