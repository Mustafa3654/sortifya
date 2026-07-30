<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\TelegramService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles the Approve / Reject buttons on a payout alert.
 *
 * Register the hook with:
 *   POST https://api.telegram.org/bot<TOKEN>/setWebhook
 *        url=https://your-host/api/telegram/webhook
 *        secret_token=<TELEGRAM_WEBHOOK_SECRET>
 *
 * Telegram then echoes that secret on every call, which is what
 * distinguishes a real callback from anyone who guessed the URL.
 */
class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegram,
        private readonly WalletService $wallet,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->isAuthentic($request)) {
            Log::warning('Rejected a Telegram webhook with a bad secret token.', ['ip' => $request->ip()]);

            return response()->json(['ok' => false], 403);
        }

        $callback = $request->input('callback_query');

        // Anything that is not a button press (a plain message, an edit) is
        // acknowledged and ignored — Telegram retries on a non-200.
        if (! is_array($callback)) {
            return response()->json(['ok' => true]);
        }

        [$action, $id] = $this->parse((string) ($callback['data'] ?? ''));

        if ($action === null) {
            return response()->json(['ok' => true]);
        }

        $withdrawal = Withdrawal::with('user')->find($id);

        if (! $withdrawal) {
            $this->telegram->answerCallback((string) $callback['id'], 'That payout no longer exists.', true);

            return response()->json(['ok' => true]);
        }

        if ($withdrawal->isSettled()) {
            $this->telegram->answerCallback(
                (string) $callback['id'],
                'Already '.$withdrawal->status->value.'.',
                true,
            );

            return response()->json(['ok' => true]);
        }

        $decidedBy = trim(sprintf(
            '%s %s',
            $callback['from']['first_name'] ?? 'Telegram',
            $callback['from']['last_name'] ?? '',
        ));

        $withdrawal = match ($action) {
            'approve' => $this->wallet->completeWithdrawal($withdrawal, "Approved from Telegram by {$decidedBy}"),
            'reject' => $this->wallet->rejectWithdrawal($withdrawal, "Declined from Telegram by {$decidedBy}"),
        };

        $this->telegram->answerCallback(
            (string) $callback['id'],
            $action === 'approve' ? 'Marked as paid.' : 'Declined and refunded.',
        );

        // Rewrite the original alert so the chat shows the outcome, not buttons.
        if (isset($callback['message']['chat']['id'], $callback['message']['message_id'])) {
            $this->telegram->settleWithdrawalMessage(
                $withdrawal->refresh(),
                (int) $callback['message']['chat']['id'],
                (int) $callback['message']['message_id'],
                $decidedBy,
            );
        }

        return response()->json(['ok' => true]);
    }

    /** Constant-time comparison against the configured secret. */
    private function isAuthentic(Request $request): bool
    {
        $expected = (string) config('services.telegram.secret_token');

        if ($expected === '') {
            // Refuse rather than run an open endpoint that moves money.
            Log::warning('TELEGRAM_WEBHOOK_SECRET is not set; the webhook is closed.');

            return false;
        }

        return hash_equals($expected, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'));
    }

    /**
     * Splits `payout_approve:12` into an action and an id.
     *
     * @return array{0: 'approve'|'reject'|null, 1: int|null}
     */
    private function parse(string $data): array
    {
        if (! preg_match('/^payout_(approve|reject):(\d+)$/', $data, $matches)) {
            return [null, null];
        }

        return [$matches[1], (int) $matches[2]];
    }
}
