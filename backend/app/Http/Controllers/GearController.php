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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGearRequest $request, Gear $gear)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gear $gear)
    {
        //
    }
}
