<?php

namespace App\Filament\Widgets;

use App\Enums\SubmissionStatus;
use App\Enums\TaskStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Submission;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The four numbers an admin acts on.
 *
 * Two are work queues (things waiting on a human), one is supply, and one is
 * the platform's liability. Deliberately not vanity metrics — every tile here
 * either demands an action or answers "what do we owe".
 */
class PlatformOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    // Four cheap aggregates. Filament defers widgets by default, which buys a
    // round-trip and a skeleton flash for nothing here.
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $pendingReviews = Submission::where('status', SubmissionStatus::Pending->value)->count();

        $oldestReview = Submission::where('status', SubmissionStatus::Pending->value)
            ->oldest()
            ->value('created_at');

        $pendingPayouts = Withdrawal::where('status', WithdrawalStatus::Pending->value);
        $payoutCount = (clone $pendingPayouts)->count();
        $payoutTotal = (float) (clone $pendingPayouts)->sum('amount');

        $openTasks = Task::where('status', TaskStatus::Available->value)->count();
        $heldTasks = Task::where('status', TaskStatus::Assigned->value)->count();

        // Every unspent dollar on the platform: what we owe workers today.
        $liability = (float) Transaction::sum('amount');

        return [
            Stat::make('Waiting on review', (string) $pendingReviews)
                ->description($oldestReview
                    ? 'Oldest '.$oldestReview->diffForHumans()
                    : 'Nothing in the queue')
                ->descriptionIcon($pendingReviews > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->color($pendingReviews > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Resources\SubmissionResource::getUrl('index')),

            Stat::make('Payouts to send', '$'.number_format($payoutTotal, 2))
                ->description($payoutCount === 1 ? '1 request waiting' : "{$payoutCount} requests waiting")
                ->descriptionIcon($payoutCount > 0 ? 'heroicon-m-banknotes' : 'heroicon-m-check-circle')
                ->color($payoutCount > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Resources\WithdrawalResource::getUrl('index')),

            Stat::make('Open tasks', (string) $openTasks)
                ->description($heldTasks === 1 ? '1 more held by a worker' : "{$heldTasks} more held by workers")
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($openTasks > 0 ? 'success' : 'danger')
                ->url(\App\Filament\Resources\TaskResource::getUrl('index')),

            Stat::make('Owed to workers', '$'.number_format($liability, 2))
                ->description('Unspent balances across all accounts')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('gray'),
        ];
    }
}
