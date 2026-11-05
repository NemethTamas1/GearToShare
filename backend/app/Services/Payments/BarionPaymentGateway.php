<?php

namespace App\Services\Payments;

use App\Models\Rental;
use Error;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BarionPaymentGateway implements PaymentGateway
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $posKey,
        private readonly string $payee,
    ) {}

    public function startPayment(Rental $rental): PaymentResult
    {
        $rental->loadMissing('gear');
        $frontend = config('app.frontend_url');
        $total = (int) round($rental->total_price);

        $response = Http::post($this->baseUrl . '/v2/Payment/Start', [
            'POSKey' => $this->posKey,
            'PaymentType' => 'Immediate',
            'GuestCheckOut' => true,
            'FundingSources' => ['All'],
            'PaymentRequestId' => 'rental-' . $rental->id,
            'RedirectUrl' => $frontend . "/rentals/{$rental->id}/payment-return",
            'CallbackUrl'      => config('services.barion.callback_url'),
            'Locale'           => 'hu-HU',
            'Currency'         => 'HUF',
            'Transactions' => [[
                'POSTransactionId' => 'rental-' . $rental->id . '-tx',
                'Payee' => $this->payee,
                'Total' => $total,
                'Items' => [[
                    'Name' => 'Bérlés: ' . $rental->id . '-tx',
                    'Description' => 'Eszközbérlés' . $rental->start_date . ' - ' . $rental->end_date,
                    'Quantity' => 1,
                    'Unit' => 'db',
                    'UnitPrice' => $total,
                    'ItemTotal' => $total,
                ]],
            ]],
        ]);

        if ($response->failed() || empty($response->json('PaymentId'))) {
            Log::warning('Barion start failed', [
                'status' => $response->status(),
                'body'   => $response->json() ?? $response->body(),
            ]);

            throw new RuntimeException('Barion fizetés indítása sikertelen.');
        }

        return new PaymentResult(
            paymentId: $response->json('PaymentId'),
            redirectUrl: $response->json('GatewayUrl'),
        );
    }

    public function isPaid(string $paymentId): bool
    {
        $response = Http::withHeaders(['x-pos-key' => $this->posKey])
            ->get($this->baseUrl . "/v4/payment/{$paymentId}/paymentstate");

        if ($response->failed()) {
            return false;
        }

        return $response->json('Status') === 'Succeeded';
    }
}
