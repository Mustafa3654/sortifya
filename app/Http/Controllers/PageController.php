<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * The static supporting pages.
 *
 * Their bodies live in lang/{en,ar}/sortifya.php as arrays of sections, so both
 * languages stay in step and a wording change never means editing markup.
 */
class PageController extends Controller
{
    public function terms(): View
    {
        return $this->legal('terms', 'file-text');
    }

    public function privacy(): View
    {
        return $this->legal('privacy', 'lock');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    private function legal(string $page, string $icon): View
    {
        $copy = __("sortifya.pages.{$page}");

        return view('pages.legal', [
            'page' => $page,
            'icon' => $icon,
            'copy' => $copy,
            'sections' => array_map(
                fn (array $section) => [
                    'heading' => $section['heading'],
                    'body' => strtr($section['body'], $this->placeholders()),
                ],
                $copy['sections'],
            ),
        ]);
    }

    /**
     * The legal copy quotes the platform's own rules — hold time, payout floor,
     * who operates it. Reading them from config keeps the pages honest when a
     * setting changes, instead of leaving a stale number in a paragraph.
     *
     * @return array<string, string>
     */
    private function placeholders(): array
    {
        return [
            ':company' => config('sortifya.company.legal_name'),
            ':country' => config('sortifya.company.country'),
            ':email' => config('sortifya.contact.support_email') ?: config('sortifya.contact.to'),
            ':hold' => (string) config('sortifya.task_hold_minutes'),
            ':max' => (string) config('sortifya.max_concurrent_tasks'),
            ':minimum' => '$'.number_format((float) config('sortifya.minimum_withdrawal'), 2),
        ];
    }
}
