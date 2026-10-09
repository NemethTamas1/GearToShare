<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RentalDetailResource extends RentalResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $revealed = in_array($this->status, ['accepted', 'active'], true);
        $isRenter = $user->id === $this->renter_id;
        $isOwner = $user->id === $this->gear->user_id;

        return array_merge(parent::toArray($request), [
            'contact' => $this->when(
                $revealed,
                fn() => $isRenter
                    ? [
                        'address' => $this->gear->address,
                        'phone' => $this->gear->user->phone,
                    ]
                    : [
                        'phone' => $this->renter->phone,
                    ]
            ),
            'handover_token' => $this->when(
                $this->status === 'accepted' && $isOwner,
                fn() => $this->handover_token
            ),
        ]);
    }
}
