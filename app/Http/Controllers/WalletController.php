<?php

namespace App\Http\Controllers;

use App\Enums\WithdrawalMethod;
use App\Services\TelegramService;
use App\Services\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $wallet,
        private readonly TelegramService $telegram,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('wallet', [
            'balance' => $this->wallet->balance($user),
            'lifetimeEarned' => $user->lifetimeEarned(),
            'lifetimeWithdrawn' => $user->lifetimeWithdrawn(),
            'pendingEarnings' => $user->pendingEarnings(),
            'hasPending' => $user->hasPendingWithdrawal(),
            'minimum' => $this->wallet->minimumWithdrawal(),
            'transactions' => $user->transactions()->latest()->paginate(15),
            'withdrawals' => $user->withdrawals()->latest()->take(8)->get(),
        ]);
    }

    /**
     * Requests a payout.
     *
     * The money is debited the moment the request is created, so the balance
     * on screen already reflects it and the same funds cannot be requested
     * twice from two tabs.
     */
    public function withdraw(Request $request): RedirectResponse
    {
        $user = $request->user();
        $balance = $this->wallet->balance($user);
        $minimum = $this->wallet->minimumWithdrawal();

        if ($user->hasPendingWithdrawal()) {
            return back()->with('warning', __('sortifya.wallet.has_pending'));
        }

        if ($balance < $minimum) {
            return back()->with('warning', __('sortifya.wallet.below_min', [
                'min' => '$'.number_format($minimum, 2),
                'balance' => '$'.number_format($balance, 2),
            ]));
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', "min:{$minimum}", "max:{$balance}", 'decimal:0,2'],
            'method' => ['required', Rule::enum(WithdrawalMethod::class)],
            'full_name' => ['required', 'string', 'max:120'],
            'phone_number' => ['nullable', 'string', 'max:32', 'regex:/^[0-9+\-\s()]+$/'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $method = WithdrawalMethod::from($validated['method']);

        // Whish and cash payouts are useless without a number to reach.
        if ($method->requiresPhone() && blank($validated['phone_number'] ?? null)) {
            throw ValidationException::withMessages([
                'phone_number' => __('validation.required', ['attribute' => __('sortifya.wallet.payout_phone')]),
            ]);
        }

        try {
            $withdrawal = $this->wallet->requestWithdrawal(
                $user,
                (float) $validated['amount'],
                $method,
                [
                    'full_name' => $validated['full_name'],
                    'phone_number' => $validated['phone_number'] ?? null,
                    'note' => $validated['note'] ?? null,
                ],
            );
        } catch (RuntimeException $e) {
            // The balance moved underneath the form between render and submit.
            return back()->with('warning', $e->getMessage() === 'below_minimum'
                ? __('sortifya.wallet.below_min', [
                    'min' => '$'.number_format($minimum, 2),
                    'balance' => '$'.number_format($this->wallet->balance($user), 2),
                ])
                : __('sortifya.wallet.insufficient', [
                    'balance' => '$'.number_format($this->wallet->balance($user), 2),
                ]));
        }

        // Best-effort: a silent Telegram never blocks a payout.
        $this->telegram->announceWithdrawal($withdrawal);

        return redirect()
            ->route('wallet')
            ->with('success', __('sortifya.wallet.requested'))
            ->with('celebrate', true);
    }
}
