<?php

namespace App\Services\Payments;

class PaymentResult
{
    public function __construct(
        public readonly string $paymentId,   // ez kerül a rentals.barion_payment_id oszlopba
        public readonly string $redirectUrl, // ide irányítjuk a bérlőt fizetni
    ) {}
}