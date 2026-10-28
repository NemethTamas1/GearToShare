<?php

namespace App\Services\Payments;

use App\Models\Rental;
use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGateway
{
    public function startPayment(Rental $rental): PaymentResult
    {
        return new PaymentResult(
            paymentId: 'fake_' . Str::uuid(),
            redirectUrl: config('app.frontend_url', 'http://localhost:5173') . "/rentals/{$rental->id}",
        );
    }

    public function isPaid(string $paymentId): bool
    {
        return true; // a hamis fizetés mindig sikeres
    }
}