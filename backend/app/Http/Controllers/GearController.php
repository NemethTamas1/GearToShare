<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGearRequest;
use App\Http\Requests\UpdateGearRequest;
use App\Http\Requests\UpdateGearStatusRequest;
use App\Http\Resources\GearResource;
use App\Models\Gear;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnexpectedSessionUsageException;

class GearController extends Controller
{
    public function index()
    {
        $availableGears = Gear::where("status", "available")->paginate(15);

        return GearResource::collection($availableGears);
    }

    public function store(StoreGearRequest $request)
    {
        $gear = new Gear($request->validated());
        $gear->user_id = $request->user()->id;
        $gear->save();

        return new GearResource($gear);
    }

    public function show(Gear $gear)
    {
        return new GearResource($gear);
    }

    public function update(UpdateGearRequest $request, Gear $gear)
    {
        // Későbbiekben Policy-be kiszervezni, most az MVP miatt van így.
        if ($request->user()->id != $gear->user_id) {
            abort(403, "Unauthorized action");
        }

        $data = $request->validated();

        $gear->update($data);

        return new GearResource($gear);
    }

    public function destroy(Gear $gear)
    {
        if (request()->user()->id !== $gear->user_id) {
            abort(403, "Unauthorized action.");
        }

        return ($gear->delete() ? response()->noContent() : abort(500));
    }

    public function mine(Request $request)
    {
        $gears = Gear::where('user_id', $request->user()->id)->latest()->get();

        return GearResource::collection($gears);
    }

    public function updateStatus(UpdateGearStatusRequest $request, Gear $gear)
    {
        if ($request->user()->id !== $gear->user_id) {
            abort(403, "Unauthorized action.");
        }

        $gear->status = $request->validated()["status"];
        $gear->save();

        return new GearResource($gear);
    }
}
