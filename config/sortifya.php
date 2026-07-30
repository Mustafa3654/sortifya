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
