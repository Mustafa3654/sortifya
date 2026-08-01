<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Task locking
    |--------------------------------------------------------------------------
    |
    | How long a claimed task stays locked to one worker before the scheduled
    | sweeper returns it to the queue, and how many tasks one worker may hold.
    |
    */

    'task_hold_minutes' => (int) env('SORTIFYA_TASK_HOLD_MINUTES', 45),

    'max_concurrent_tasks' => (int) env('SORTIFYA_MAX_CONCURRENT_TASKS', 1),

    /*
    |--------------------------------------------------------------------------
    | Payouts
    |--------------------------------------------------------------------------
    */

    'minimum_withdrawal' => (float) env('SORTIFYA_MIN_WITHDRAWAL', 10.00),

    'currency' => 'USD',

    'currency_symbol' => '$',

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    |
    | Submissions are private: they live on the `local` disk under
    | storage/app/private and are only ever streamed through a controller
    | that checks who is asking.
    |
    */

    'uploads' => [
        'submissions_disk' => 'local',
        'submissions_path' => 'private/submissions',

        'tasks_disk' => 'public',
        'source_path' => 'tasks/sources',
        'template_path' => 'tasks/templates',

        'max_upload_kb' => (int) env('SORTIFYA_MAX_UPLOAD_KB', 10240),
        'accepted' => ['xlsx', 'xls', 'csv'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin preview
    |--------------------------------------------------------------------------
    |
    | Rows lifted out of an uploaded spreadsheet at upload time so a reviewer
    | can judge a submission without downloading it.
    |
    */

    'preview_rows' => (int) env('SORTIFYA_PREVIEW_ROWS', 10),

    'preview_columns' => 12,

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | Where the contact form lands. Falls back to the platform's own from-address
    | so a missing CONTACT_TO never sends a worker's message into the void.
    |
    | `support_email` and `support_phone` are printed on the contact page; leave
    | the phone blank to hide it.
    |
    */

    // `?:` rather than env()'s second argument: the default there only applies
    // when the key is absent, and a key present-but-empty (CONTACT_TO=) is the
    // normal state of a freshly copied .env.
    'contact' => [
        'to' => env('CONTACT_TO') ?: env('MAIL_FROM_ADDRESS'),
        'support_email' => env('CONTACT_SUPPORT_EMAIL') ?: env('CONTACT_TO') ?: env('MAIL_FROM_ADDRESS'),
        'support_phone' => env('CONTACT_SUPPORT_PHONE') ?: null,
        // Replies are promised within this many working hours on the page.
        'response_hours' => (int) env('CONTACT_RESPONSE_HOURS', 24),
    ],

    /*
    |--------------------------------------------------------------------------
    | Company
    |--------------------------------------------------------------------------
    |
    | Printed in the legal pages. Fill these in before publishing them — the
    | terms are unenforceable if they do not name who is behind the service.
    |
    */

    'company' => [
        'legal_name' => env('COMPANY_LEGAL_NAME', 'Sortifya'),
        'address' => env('COMPANY_ADDRESS'),
        'country' => env('COMPANY_COUNTRY', 'Lebanon'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Legal
    |--------------------------------------------------------------------------
    |
    | Printed at the top of the terms and privacy pages. Bump it whenever you
    | change either — a stale date on a legal page is worse than none.
    |
    */

    'legal_updated' => env('LEGAL_UPDATED', '2026-08-01'),

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    */

    'locales' => [
        'en' => ['label' => 'English', 'native' => 'English', 'dir' => 'ltr'],
        'ar' => ['label' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Landing page figures
    |--------------------------------------------------------------------------
    |
    | The counters on the home page. Rows and payouts come from the database;
    | these are the floors added to them so a fresh install does not present
    | itself as an empty platform.
    |
    */

    'stats_baseline' => [
        'rows' => (int) env('SORTIFYA_BASELINE_ROWS', 0),
        'paid' => (float) env('SORTIFYA_BASELINE_PAID', 0),
    ],
];
