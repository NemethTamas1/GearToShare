<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGearRequest;
use App\Http\Requests\UpdateGearRequest;
use App\Http\Resources\GearResource;
use App\Models\Gear;

class GearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $availableGears = Gear::where("status", "available")->paginate(15);

        return GearResource::collection($availableGears);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGearRequest $request)
    {
        $gear = Gear::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return new GearResource($gear);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gear $gear)
    {
        return new GearResource($gear);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGearRequest $request, Gear $gear)
    {
        // Későbbiekben Policy-be kiszervezni, most az MVP miatt van így.
        if($request->user()->id != $gear->user_id) {
            abort(403, "Unauthorized action");
        }

        $data = $request->validated();

        $gear->update($data);

        return new GearResource($gear);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gear $gear)
    {
        //
    }
}
