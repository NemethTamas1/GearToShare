<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use App\Services\Payments\BarionPaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
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

    public function test_start_payment_throws_when_barion_returns_error(): void
    {
        Http::fake([
            'barion.test/v2/Payment/Start' => Http::response([
                'Errors' => [
                    ["ErrorCode" => 'ShopIsInDraftState', 'Title' => 'Your shop is in draft state.'],
                ]
            ], 401),
        ]);

        $this->expectException(RuntimeException::class);

        $this->gateway()->startPayment($this->rental());
    }

    public function test_is_paid_returns_true_when_status_is_succeeded(): void
    {
        Http::fake([
            'barion.test/v4/payment/abc-123/paymentstate' => Http::response([
                "Status" => "Succeeded",
            ], 200),
        ]);

        $this->assertTrue($this->gateway()->isPaid('abc-123'));
    }

    public function test_is_paid_returns_false_when_status_is_prepared(): void
    {
        Http::fake([
            'barion.test/v4/payment/abc-123/paymentstate' => Http::response([
                "Status" => "Prepared"
            ], 200),
        ]);

        $this->assertFalse($this->gateway()->isPaid('abc-123'));
    }

    public function test_is_paid_returns_false_when_status_is_canceled(): void
    {
        Http::fake([
            'barion.test/v4/payment/abc-123/paymentstate' => Http::response([
                "Status" => "Canceled"
            ], 200),
        ]);

        $this->assertFalse($this->gateway()->isPaid('abc-123'));
    }

    public function test_is_paid_returns_false_when_barion_returns_server_error(): void
    {
        Http::fake([
            'barion.test/v4/payment/abc-123/paymentstate' => Http::response([], 500),
        ]);

        $this->assertFalse($this->gateway()->isPaid('abc-123'));
    }
}
