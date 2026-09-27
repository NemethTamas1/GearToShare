<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGearRequest;
use App\Http\Requests\UpdateGearRequest;
use App\Http\Resources\GearResource;
use App\Models\Gear;

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
}
