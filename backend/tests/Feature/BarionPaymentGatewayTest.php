<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use App\Services\Payments\BarionPaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BarionPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    private function gateway(): BarionPaymentGateway
    {
        return new BarionPaymentGateway('http://barion.test', 'teszt-kulcs', "teszt@mail.hu");
    }

    private function rental(): Rental
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        return Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'total_price' => '7000.00',
        ]);
    }

    public function test_start_payment_returns_payment_id_and_gateway_url(): void
    {
        Http::fake([
            'barion.test/v2/Payment/Start' => Http::response([
                'PaymentId' => 'abc-123',
                'GatewayUrl' => 'https://pay.test/abc-123',
            ], 200),
        ]);

        $result = $this->gateway()->startPayment($this->rental());

        $this->assertSame('abc-123', $result->paymentId);
        $this->assertSame('https://pay.test/abc-123', $result->redirectUrl);
    }

    //test_start_payment_sends_correct_request: Http::fake ugyanúgy,
    //majd Http::assertSent(...)-ben ellenőrizd, hogy a Currency HUF,
    //a Transactions[0]['Total'] pedig 7000 (egész szám, nem 7000.00),
    //és a POSKey megegyezik a konstruktornak adottal.
    public function test_start_payment_sends_correct_request(): void
    {
        Http::fake([
            'barion.test/v2/Payment/Start' => Http::response([
                'PaymentId' => 'abc-123',
                'GatewayUrl' => 'https://pay.test/abc-123',
            ], 200),
        ]);

        $this->gateway()->startPayment($this->rental());

        Http::assertSent(function (Request $request) {
            return $request['Currency'] === 'HUF'
                && $request['Transactions'][0]['Total'] === 7000
                && $request['POSKey'] === 'teszt-kulcs';
        });
    }
}
