<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Enums\TransactionType;
use App\Models\Submission;
use App\Models\Task;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * The public landing page.
     *
     * Reachable by anyone. Guests are shown the live queue and prompted to
     * register only at the point they try to claim something — never bounced
     * to /login for looking.
     */
    public function __invoke(): View
    {
        return view('home', [
            'tasks' => Task::available()->latest()->take(6)->get(),
            'stats' => $this->stats(),
        ]);
    }

    /**
     * Figures for the hero counters.
     *
     * Cached for a minute: the landing page is the most-hit route on the
     * platform and none of these numbers need to be accurate to the second.
     *
     * @return array{rows: int, paid: float, open_tasks: int, approval_rate: int}
     */
    private function stats(): array
    {
        return Cache::remember('sortifya.home.stats', now()->addMinute(), function () {
            $approved = Submission::approved()->count();
            $rejected = Submission::where('status', SubmissionStatus::Rejected->value)->count();
            $reviewed = $approved + $rejected;

            // Row counts were captured into the preview blob at upload time.
            $rows = (int) Submission::approved()
                ->select(DB::raw("COALESCE(SUM(CAST(JSON_EXTRACT(parsed_preview_data, '$.total_rows') AS UNSIGNED)), 0) AS total"))
                ->value('total');

            $paid = (float) Transaction::where('type', TransactionType::TaskReward->value)->sum('amount');

            return [
                'rows' => $rows + (int) config('sortifya.stats_baseline.rows'),
                'paid' => round($paid + (float) config('sortifya.stats_baseline.paid'), 2),
                'open_tasks' => Task::available()->count(),
                // A brand-new install has reviewed nothing; show the target, not 0%.
                'approval_rate' => $reviewed > 0 ? (int) round($approved / $reviewed * 100) : 96,
            ];
        });
    }
}
