<?php

namespace App\Services\Payments;

use App\Models\Rental;

interface PaymentGateway
{
    /** Fizetés indítása; visszaadja a fizetés azonosítóját és a bérlő átirányítási URL-jét. */
    public function startPayment(Rental $rental): PaymentResult;

    /** Lekérdezi, hogy a megadott fizetés sikeres volt-e. */
    public function isPaid(string $paymentId): bool;
}