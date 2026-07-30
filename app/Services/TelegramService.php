<?php

namespace App\Services;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pushes payout requests to an admin Telegram chat and acts on the reply.
 *
 * Telegram is a convenience channel, never a dependency: if the bot is
 * unconfigured or the API is down, every method degrades to a logged warning
 * and the payout still sits in /admin waiting for someone. Nothing here is
 * allowed to fail a worker's request.
 */
class TelegramService
{
    public function configured(): bool
    {
        return filled(config('services.telegram.bot_token'))
            && filled(config('services.telegram.admin_chat_id'));
    }

    /*
    |--------------------------------------------------------------------------
    | Payout alerts
    |--------------------------------------------------------------------------
    */

    /** Announces a new payout request with Approve / Reject buttons. */
    public function announceWithdrawal(Withdrawal $withdrawal): bool
    {
        return $this->send(
            $this->withdrawalMessage($withdrawal),
            [
                'inline_keyboard' => [[
                    ['text' => '✅ Approve', 'callback_data' => "payout_approve:{$withdrawal->id}"],
                    ['text' => '✖ Reject', 'callback_data' => "payout_reject:{$withdrawal->id}"],
                ]],
            ],
        );
    }

    /**
     * Rewrites the original alert once a decision is made, so the chat shows
     * the outcome instead of buttons that no longer do anything.
     */
    public function settleWithdrawalMessage(Withdrawal $withdrawal, int $chatId, int $messageId, string $decidedBy): bool
    {
        $verdict = $withdrawal->status->value === 'completed'
            ? "✅ <b>Approved and marked paid</b>"
            : "✖ <b>Declined — {$this->escape((string) $withdrawal->amount)} USD refunded</b>";

        return $this->call('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'parse_mode' => 'HTML',
            'text' => $this->withdrawalMessage($withdrawal)
                ."\n\n".$verdict
                ."\n<i>by {$this->escape($decidedBy)} · ".now()->format('d M Y, H:i').'</i>',
        ]);
    }

    /** Stops the button spinner in the Telegram client. */
    public function answerCallback(string $callbackId, string $text = '', bool $alert = false): bool
    {
        return $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackId,
            'text' => $text,
            'show_alert' => $alert,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Message building
    |--------------------------------------------------------------------------
    */

    private function withdrawalMessage(Withdrawal $withdrawal): string
    {
        $user = $withdrawal->user;

        $lines = [
            '💸 <b>Payout request</b> #'.$withdrawal->id,
            '',
            '<b>Amount</b>  $'.number_format((float) $withdrawal->amount, 2),
            '<b>Method</b>  '.$this->escape($withdrawal->method->getLabel()),
            '<b>Worker</b>  '.$this->escape($user?->name ?? 'Unknown').' · '.$this->escape($user?->email ?? '—'),
        ];

        if ($withdrawal->payoutName()) {
            $lines[] = '<b>Pay to</b>  '.$this->escape($withdrawal->payoutName());
        }

        if ($withdrawal->payoutPhone()) {
            $lines[] = '<b>Phone</b>  '.$this->escape($withdrawal->payoutPhone());
        }

        if ($withdrawal->payoutNote()) {
            $lines[] = '<b>Note</b>  '.$this->escape($withdrawal->payoutNote());
        }

        $lines[] = '';
        $lines[] = '<b>Balance after</b>  $'.number_format($user?->balance() ?? 0, 2);
        $lines[] = '<i>Requested '.$withdrawal->created_at->format('d M Y, H:i').'</i>';

        return implode("\n", $lines);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /*
    |--------------------------------------------------------------------------
    | Transport
    |--------------------------------------------------------------------------
    */

    /** @param  array<string, mixed>|null  $replyMarkup */
    private function send(string $text, ?array $replyMarkup = null): bool
    {
        $payload = [
            'chat_id' => config('services.telegram.admin_chat_id'),
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->call('sendMessage', $payload);
    }

    /** @param  array<string, mixed>  $payload */
    private function call(string $method, array $payload): bool
    {
        if (! $this->configured()) {
            Log::warning('Telegram is not configured; skipping alert.', ['method' => $method]);

            return false;
        }

        try {
            $response = Http::timeout((int) config('services.telegram.timeout'))
                ->asForm()
                ->post(
                    sprintf(
                        '%s/bot%s/%s',
                        rtrim((string) config('services.telegram.api_url'), '/'),
                        config('services.telegram.bot_token'),
                        $method,
                    ),
                    $payload,
                );

            if ($response->failed()) {
                Log::warning('Telegram call failed.', [
                    'method' => $method,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            // A dead webhook must never take a payout request down with it.
            Log::warning('Telegram is unreachable.', ['method' => $method, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
