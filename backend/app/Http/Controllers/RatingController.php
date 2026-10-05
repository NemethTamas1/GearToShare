<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use App\Models\Rental;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request, Rental $rental)
    {
        $user = $request->user();
        $ownerId = $rental->gear->user_id;

        if ($user->id !== $rental->renter_id && $user->id !== $ownerId) {
            abort(403, "Unauthorized action");
        }

        if ($rental->status !== 'completed') {
            abort(422, 'Csak lezárt bérlés értékelhető.');
        }

        if ($rental->ratings()->where('rater_id', $user->id)->exists()) {
            abort(422, 'Ezt a bérlést már értékelted.');
        }

        $rating = new Rating($request->validated());
        $rating->rental_id = $rental->id;
        $rating->rater_id = $user->id;
        $rating->rated_id = $user->id === $rental->renter_id ? $ownerId : $rental->renter_id;
        $rating->save();

        return new RatingResource($rating);
    }

    public function received(Request $request)
    {
        $ratings = Rating::where('rated_id', $request->user()->id)
            ->with('rater')
            ->latest()
            ->get();

        return response()->json([
            'average' => $ratings->isEmpty() ? null : round($ratings->avg('score'), 1),
            'count' => $ratings->count(),
            'data' => RatingResource::collection($ratings),
        ]);
    }
}
