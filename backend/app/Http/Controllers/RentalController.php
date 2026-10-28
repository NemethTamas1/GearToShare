<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Http\Resources\RentalDetailResource;
use App\Http\Resources\RentalResource;
use App\Models\Gear;
use App\Models\Rental;
use App\Services\Payments\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function show(Request $request, Rental $rental)
    {
        $user = $request->user();
        $rental->load(['gear.user', 'renter']);

        $isRenter = $user->id === $rental->renter_id;
        $isOwner = $user->id === $rental->gear->user_id;

        if (! $isRenter && ! $isOwner) {
            abort(403, "Unauthorized action.");
        }

        return new RentalDetailResource($rental);
    }

    public function update(UpdateRentalRequest $request, Rental $rental)
    {
        if ($rental->gear->user_id !== $request->user()->id) {
            abort(403, "Unauthorized action.");
        }

        if ($rental->status !== 'pending') {
            abort(422, "Csak függőben lévő kérelem állapota módosítható.");
        }

        $rental->status = $request->validated('status');
        if ($rental->status === 'accepted') {
            $rental->handover_token = (string) Str::uuid();
        }

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

    public function confirmHandover(Request $request, Rental $rental, PaymentGateway $payments)
    {
        $request->validate(['token' => ['required', 'string']]);

        if ($request->user()->id !== $rental->renter_id) {
            abort(403, "Unauthorized action.");
        }

        if ($rental->status !== 'accepted') {
            abort(422, "Ez a bérlés nem vehető át.");
        }

        if (! $rental->handover_token || ! hash_equals($rental->handover_token, $request->input('token'))) {
            abort(422, "Érvénytelen átvételi kód.");
        }

        $result = $payments->startPayment($rental);

        $rental->barion_payment_id = $result->paymentId;
        $rental->save();

        return response()->json(['redirect_url' => $result->redirectUrl]);
    }

    public function completePayment(Request $request, Rental $rental, PaymentGateway $payments)
    {
        if ($request->user()->id !== $rental->renter_id) {
            abort(403, "Unauthorized action.");
        }

        if ($rental->status === 'active') {
            return new RentalResource($rental);
        }

        if ($rental->status !== 'accepted') {
            abort(422, "Ez a bérlés nem véglegesíthető.");
        }

        if (!$rental->barion_payment_id) {
            abort(422, "Ehhez a bérléshez nincs elindított fizetés.");
        }

        if (! $payments->isPaid($rental->barion_payment_id)) {
            abort(422, "A fizetés még nem érkezett meg.");
        }

        DB::transaction(function () use ($rental) {
            $rental->status = 'active';
            $rental->handover_confirmed_at = now();
            $rental->handover_token = null;
            $rental->save();
        });

        return new RentalResource($rental);
    }

    public function mine(Request $request)
    {
        $userId = $request->user()->id;

        $rentals = Rental::where('renter_id', $userId)
            ->orWhereHas('gear', fn($q) => $q->where('user_id', $userId))
            ->with(['gear', 'renter'])
            ->latest()
            ->get();

        return RentalResource::collection($rentals);
    }
}
