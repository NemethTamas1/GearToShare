<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\Gear;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreRentalRequest $request)
    {
        $gear = Gear::findOrFail($request->validated('gear_id'));

        if ($gear->user_id === $request->user()->id) {
            abort(422, "Nem bérelheted ki a saját eszközödet.");
        }

        $days = Carbon::parse($request->validated('start_date'))
            ->diffInDays(Carbon::parse($request->validated('end_date')));

        $rental = new Rental($request->validated());
        $rental->renter_id = $request->user()->id;
        $rental->total_price = $gear->price_per_day * max($days, 1);
        $rental->save();

        return new RentalResource($rental);
    }

    public function show(Rental $rental)
    {
        //
    }

    public function update(UpdateRentalRequest $request, Rental $rental)
    {
        if ($rental->gear->user_id !== $request->user()->id) {
            abort(403, "Unauthorized action.");
        }

        if ($rental->status !== 'pending') {
            abort(422, "Csak függőben lévő kérelem állapota módosítható.");
        }

        $rental->status = $request->validated("status");
        $rental->save();

        return new RentalResource($rental);
    }

    public function destroy(Rental $rental)
    {
        //
    }

    public function incoming(Request $request)
    {
        $rentals = Rental::whereHas('gear', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })
            ->where('status', 'pending')
            ->with(['gear', 'renter'])
            ->latest()
            ->get();

        return RentalResource::collection($rentals);
    }
}
