<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use App\Enums\WithdrawalMethod;
use App\Enums\WithdrawalStatus;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'test-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.telegram.bot_token', 'test-token');
        config()->set('services.telegram.admin_chat_id', '123');
        config()->set('services.telegram.secret_token', self::SECRET);

        Http::fake(['*' => Http::response(['ok' => true])]);
    }

    private function pendingWithdrawal(): \App\Models\Withdrawal
    {
        $wallet = app(WalletService::class);
        $user = User::factory()->create();
        $wallet->credit($user, 25.00, TransactionType::Bonus, 'Opening balance');

        return $wallet->requestWithdrawal($user, 10.00, WithdrawalMethod::WhishMoney, [
            'full_name' => 'Rania Haddad',
            'phone_number' => '+961 71 000 000',
        ]);
    }

    private function pressButton(string $data): array
    {
        return [
            'callback_query' => [
                'id' => 'cb-1',
                'from' => ['first_name' => 'Admin', 'last_name' => 'One'],
                'message' => ['message_id' => 7, 'chat' => ['id' => 123]],
                'data' => $data,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Authenticity
    |--------------------------------------------------------------------------
    */

    public function test_a_call_without_the_secret_header_is_refused(): void
    {
        $withdrawal = $this->pendingWithdrawal();

        $this->postJson('/api/telegram/webhook', $this->pressButton("payout_approve:{$withdrawal->id}"))
            ->assertForbidden();

        $this->assertSame(WithdrawalStatus::Pending, $withdrawal->fresh()->status);
    }

    public function test_a_call_with_the_wrong_secret_is_refused(): void
    {
        $withdrawal = $this->pendingWithdrawal();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'wrong')
            ->postJson('/api/telegram/webhook', $this->pressButton("payout_approve:{$withdrawal->id}"))
            ->assertForbidden();

        $this->assertSame(WithdrawalStatus::Pending, $withdrawal->fresh()->status);
    }

    /** With no secret configured the endpoint stays closed rather than open. */
    public function test_the_endpoint_is_closed_when_no_secret_is_configured(): void
    {
        config()->set('services.telegram.secret_token', '');

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', '')
            ->postJson('/api/telegram/webhook', $this->pressButton('payout_approve:1'))
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | Decisions
    |--------------------------------------------------------------------------
    */

    public function test_approving_from_telegram_marks_the_payout_paid(): void
    {
        $withdrawal = $this->pendingWithdrawal();
        $balanceBefore = app(WalletService::class)->balance($withdrawal->user);

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', $this->pressButton("payout_approve:{$withdrawal->id}"))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(WithdrawalStatus::Completed, $withdrawal->fresh()->status);
        // Already debited at request time; approving moves nothing further.
        $this->assertSame($balanceBefore, app(WalletService::class)->balance($withdrawal->user->fresh()));
    }

    public function test_rejecting_from_telegram_refunds_the_worker(): void
    {
        $withdrawal = $this->pendingWithdrawal();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', $this->pressButton("payout_reject:{$withdrawal->id}"))
            ->assertOk();

        $this->assertSame(WithdrawalStatus::Rejected, $withdrawal->fresh()->status);
        $this->assertSame(25.00, app(WalletService::class)->balance($withdrawal->user->fresh()));
    }

    public function test_replaying_a_callback_does_not_double_refund(): void
    {
        $withdrawal = $this->pendingWithdrawal();
        $payload = $this->pressButton("payout_reject:{$withdrawal->id}");

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', $payload)->assertOk();

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', $payload)->assertOk();

        $this->assertSame(25.00, app(WalletService::class)->balance($withdrawal->user->fresh()));
        $this->assertSame(2, $withdrawal->transactions()->count());
    }

    public function test_unrecognised_callback_data_is_ignored(): void
    {
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', $this->pressButton('drop table users'))
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_a_non_callback_update_is_acknowledged(): void
    {
        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', self::SECRET)
            ->postJson('/api/telegram/webhook', ['message' => ['text' => 'hello']])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }
}
