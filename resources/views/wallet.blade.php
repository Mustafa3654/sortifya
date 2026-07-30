<x-layouts.app :title="__('sortifya.wallet.title')">

    @php
        $canWithdraw = ! $hasPending && $balance >= $minimum;
        $progress = $minimum > 0 ? min(100, (int) round($balance / $minimum * 100)) : 100;
    @endphp

    <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ambient -top-10 start-1/3 h-64 w-96" aria-hidden="true"></div>

        <x-page-header
            :eyebrow="__('sortifya.nav.wallet')"
            :heading="__('sortifya.wallet.title')"
            :body="__('sortifya.wallet.subtitle')"
        />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-tile icon="wallet" tone="money" :label="__('sortifya.wallet.available')"
                         :value="'$'.number_format($balance, 2)" :delay="0" />
            <x-stat-tile icon="clock" tone="wait" :label="__('sortifya.wallet.pending_payout')"
                         :value="'$'.number_format($pendingEarnings, 2)" :delay="70" />
            <x-stat-tile icon="trending-up" :label="__('sortifya.wallet.lifetime')"
                         :value="'$'.number_format($lifetimeEarned, 2)" :delay="140" />
            <x-stat-tile icon="arrow-up-right" :label="__('sortifya.wallet.withdrawn')"
                         :value="'$'.number_format($lifetimeWithdrawn, 2)" :delay="210" />
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-12">

            {{-- ── Ledger ──
                 min-w-0: a grid item defaults to min-width:auto, which refuses
                 to shrink below the ledger table's min-content width and drags
                 the whole page sideways on a phone. With it, the table's own
                 overflow-x-auto does the scrolling instead. --}}
            <section class="min-w-0 lg:col-span-7 xl:col-span-8">
                <div class="panel overflow-hidden">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                        <div>
                            <h2 class="text-base font-semibold">{{ __('sortifya.wallet.ledger_title') }}</h2>
                            <p class="mt-0.5 text-xs text-slate-400">{{ __('sortifya.wallet.ledger_body') }}</p>
                        </div>
                        <x-lucide name="list-checks" :size="18" class="text-slate-400" />
                    </div>

                    @if ($transactions->isEmpty())
                        <div class="flex flex-col items-center px-6 py-14 text-center">
                            <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-ink-850">
                                <x-lucide name="inbox" :size="24" />
                            </span>
                            <h3 class="mb-2 text-base font-semibold">{{ __('sortifya.wallet.ledger_empty_title') }}</h3>
                            <p class="max-w-sm text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.wallet.ledger_empty_body') }}</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200 dark:border-slate-800">
                                        <th class="px-5 py-3 text-start text-[10px] font-semibold uppercase tracking-widest text-slate-400">{{ __('sortifya.wallet.col_date') }}</th>
                                        <th class="px-5 py-3 text-start text-[10px] font-semibold uppercase tracking-widest text-slate-400">{{ __('sortifya.wallet.col_description') }}</th>
                                        <th class="px-5 py-3 text-start text-[10px] font-semibold uppercase tracking-widest text-slate-400">{{ __('sortifya.wallet.col_type') }}</th>
                                        <th class="px-5 py-3 text-end text-[10px] font-semibold uppercase tracking-widest text-slate-400">{{ __('sortifya.wallet.col_amount') }}</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @foreach ($transactions as $transaction)
                                        <tr class="transition-colors hover:bg-slate-900/[0.02] dark:hover:bg-white/[0.02]">
                                            <td class="whitespace-nowrap px-5 py-3.5 font-mono text-[11px] text-slate-400">
                                                {{ $transaction->created_at->format('d M Y') }}
                                                <span class="block">{{ $transaction->created_at->format('H:i') }}</span>
                                            </td>

                                            <td class="max-w-[18rem] px-5 py-3.5">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                                                                 {{ $transaction->isCredit() ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-400' }}">
                                                        <x-lucide :name="$transaction->type->lucide()" :size="13" />
                                                    </span>
                                                    <span class="truncate text-slate-700 dark:text-slate-300" title="{{ $transaction->description }}">
                                                        {{ $transaction->description }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="px-5 py-3.5">
                                                <span class="chip-mute">{{ $transaction->type->getLabel() }}</span>
                                            </td>

                                            <td class="numeric whitespace-nowrap px-5 py-3.5 text-end font-mono text-sm font-semibold
                                                       {{ $transaction->isCredit() ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}">
                                                {{ $transaction->signedAmount() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($transactions->hasPages())
                            <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-800">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @endif
                </div>

                {{-- ── Payout history ── --}}
                <div class="panel mt-6 overflow-hidden">
                    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                        <h2 class="text-base font-semibold">{{ __('sortifya.wallet.history_title') }}</h2>
                    </div>

                    @if ($withdrawals->isEmpty())
                        <p class="px-5 py-8 text-sm text-slate-500 dark:text-slate-400">{{ __('sortifya.wallet.history_empty') }}</p>
                    @else
                        <div class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($withdrawals as $withdrawal)
                                <div class="flex flex-wrap items-center gap-4 px-5 py-4">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 dark:border-slate-800 dark:bg-ink-850">
                                        <x-lucide :name="$withdrawal->method->lucide()" :size="16" />
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <p class="numeric font-mono text-sm font-semibold text-slate-900 dark:text-white">
                                            ${{ number_format((float) $withdrawal->amount, 2) }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-slate-400">
                                            {{ $withdrawal->method->getLabel() }} · {{ $withdrawal->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    @if ($withdrawal->admin_note)
                                        <p class="order-last w-full text-xs text-slate-400 sm:order-none sm:w-auto sm:max-w-xs">
                                            {{ $withdrawal->admin_note }}
                                        </p>
                                    @endif

                                    <span class="{{ $withdrawal->status->chipClass() }}">{{ $withdrawal->status->getLabel() }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            {{-- ── Request a payout ── --}}
            <section class="min-w-0 lg:col-span-5 xl:col-span-4">
                <div class="panel sticky top-24 p-6">
                    <h2 class="mb-1.5 text-base font-semibold">{{ __('sortifya.wallet.withdraw_title') }}</h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        {{ __('sortifya.wallet.withdraw_body', ['min' => '$'.number_format($minimum, 2)]) }}
                    </p>

                    @if ($hasPending)
                        <div class="flex items-start gap-3 rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3.5 text-sm text-amber-700 dark:text-amber-300">
                            <x-lucide name="clock" :size="17" class="mt-0.5 text-amber-500" />
                            {{ __('sortifya.wallet.has_pending') }}
                        </div>
                    @elseif ($balance < $minimum)
                        {{-- Below the floor: show the distance, not a dead form. --}}
                        <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                            <div class="mb-3 flex items-baseline justify-between gap-3">
                                <span class="numeric font-mono text-2xl font-semibold text-slate-900 dark:text-white">
                                    ${{ number_format($balance, 2) }}
                                </span>
                                <span class="numeric font-mono text-xs text-slate-400">
                                    / ${{ number_format($minimum, 2) }}
                                </span>
                            </div>

                            <div class="h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-ink-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700"
                                     style="width: {{ $progress }}%"></div>
                            </div>

                            <p class="mt-3.5 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                {{ __('sortifya.wallet.below_min', [
                                    'min' => '$'.number_format($minimum, 2),
                                    'balance' => '$'.number_format($balance, 2),
                                ]) }}
                            </p>

                            <a href="{{ route('dashboard') }}" class="btn-ghost mt-4 w-full !py-2 text-[13px]">
                                <x-lucide name="layout-grid" :size="15" />
                                {{ __('sortifya.dashboard.open_title') }}
                            </a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('wallet.withdraw') }}" class="space-y-5"
                              x-data="{ method: @js(old('method', 'whish_money')) }">
                            @csrf

                            <x-field
                                name="amount"
                                type="number"
                                :label="__('sortifya.wallet.amount')"
                                :value="number_format($balance, 2, '.', '')"
                                :hint="__('sortifya.wallet.amount_hint', ['max' => '$'.number_format($balance, 2)])"
                                required
                                step="0.01"
                                :min="$minimum"
                                :max="$balance"
                                class="field numeric font-mono"
                            />

                            {{-- Method as cards, not a select: four options, and
                                 the choice changes which fields matter. --}}
                            <div>
                                <span class="label">{{ __('sortifya.wallet.method') }}</span>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach (\App\Enums\WithdrawalMethod::cases() as $option)
                                        <label class="relative flex cursor-pointer items-center gap-2.5 rounded-xl border px-3 py-2.5 text-sm transition-all"
                                               :class="method === @js($option->value)
                                                   ? 'border-emerald-500 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300'
                                                   : 'border-slate-200 text-slate-600 hover:border-slate-300 dark:border-slate-800 dark:text-slate-400'">
                                            <input type="radio" name="method" value="{{ $option->value }}"
                                                   x-model="method" class="sr-only">
                                            <x-lucide :name="$option->lucide()" :size="16" />
                                            <span class="truncate font-medium">{{ $option->getLabel() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('method')
                                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-field
                                name="full_name"
                                :label="__('sortifya.wallet.payout_name')"
                                :value="auth()->user()->name"
                                required
                            />

                            <div x-show="method === 'whish_money' || method === 'cash'" x-collapse>
                                <x-field
                                    name="phone_number"
                                    type="tel"
                                    icon="smartphone"
                                    :label="__('sortifya.wallet.payout_phone')"
                                    :value="auth()->user()->phone_number"
                                    placeholder="+961 70 000 000"
                                />
                            </div>

                            <div>
                                <label for="note" class="label">
                                    {{ __('sortifya.wallet.payout_note') }}
                                    <span class="ms-1 font-normal normal-case tracking-normal text-slate-400">({{ __('sortifya.common.optional') }})</span>
                                </label>
                                <textarea id="note" name="note" rows="2" class="field resize-none"
                                          placeholder="{{ __('sortifya.wallet.payout_note_hint') }}">{{ old('note') }}</textarea>
                                @error('note')
                                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn-primary w-full !py-3">
                                <x-lucide name="hand-coins" :size="16" />
                                {{ __('sortifya.wallet.withdraw_submit') }}
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>

</x-layouts.app>
