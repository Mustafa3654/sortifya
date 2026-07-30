<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'balance' => $user->balance(),
            'pendingEarnings' => $user->pendingEarnings(),
            'approvedCount' => $user->submissions()->approved()->count(),

            // Held tasks, soonest deadline first — the thing to act on.
            'activeTasks' => $user->assignedTasks()
                ->where('status', \App\Enums\TaskStatus::Assigned->value)
                ->orderBy('expires_at')
                ->get(),

            'openTasks' => Task::available()->latest()->take(9)->get(),

            'recentSubmissions' => $user->submissions()
                ->with('task')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
