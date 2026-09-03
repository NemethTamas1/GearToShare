<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['gear_id', 'renter_id', 'start_date', 'end_date', 'total_price'])]
class Rental extends Model
{
    protected $table = "rentals";

    public $timestamps = true;

    public function gear(): BelongsTo
    {
        return $this->belongsTo(Gear::class, 'gear_id');
    }

    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}
