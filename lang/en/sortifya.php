<?php

return [

    'brand' => 'Sortifya',
    'tagline' => 'Structured data, paid by the row.',

    /*
    |--------------------------------------------------------------------------
    | Navigation & chrome
    |--------------------------------------------------------------------------
    */
    'nav' => [
        'home' => 'Home',
        'how' => 'How it works',
        'tasks' => 'Open tasks',
        'dashboard' => 'Dashboard',
        'wallet' => 'Wallet',
        'sign_in' => 'Sign in',
        'start' => 'Start earning',
        'profile' => 'Your profile',
        'admin' => 'Admin panel',
        'log_out' => 'Log out',
        'balance' => 'Balance',
        'menu' => 'Menu',
        'open_menu' => 'Open menu',
        'close_menu' => 'Close menu',
        'theme_light' => 'Switch to light',
        'theme_dark' => 'Switch to dark',
        'language' => 'Language',
    ],

    /*
    |--------------------------------------------------------------------------
    | Landing page
    |--------------------------------------------------------------------------
    */
    'home' => [
        'eyebrow' => 'Micro-task data entry',

        'hero' => [
            'title_lead' => 'Turn messy PDFs into',
            'title_accent' => 'clean Excel data',
            'title_tail' => 'and earn real cash.',
            'body' => 'Claim a scanned document, type it into a spreadsheet the way you already know how, and upload it. Approved work is credited in US dollars and paid out through Whish Money or cash.',
            'cta_primary' => 'Claim your first task',
            'cta_secondary' => 'See how it works',
            'note' => 'Free to join. No equipment beyond a spreadsheet.',
        ],

        'sheet' => [
            'source' => 'invoice-batch-042.pdf',
            'source_note' => 'Scanned source',
            'output' => 'batch-042.xlsx',
            'output_note' => 'Your submission',
            'col_date' => 'Date',
            'col_vendor' => 'Vendor',
            'col_amount' => 'Amount',
            'progress' => 'Rows committed',
        ],

        'stats' => [
            'title' => 'Where the platform stands right now',
            'rows' => 'Rows transcribed',
            'paid' => 'Paid to workers',
            'tasks' => 'Tasks open today',
            'approval' => 'Approval rate',
        ],

        'how' => [
            'eyebrow' => 'The loop',
            'title' => 'Three steps, then you get paid',
            'body' => 'Every task follows the same shape. Once you have done one, you have done all of them.',

            'step_1_title' => 'Claim and download the PDF',
            'step_1_body' => 'Pick a task from the open list. It locks to you for 45 minutes so nobody else can take it while you work.',
            'step_1_meta' => '45-minute hold',

            'step_2_title' => 'Type it into a spreadsheet',
            'step_2_body' => 'Use Excel, Numbers, or Google Sheets — whatever you already have. Most tasks ship a template so the columns match.',
            'step_2_meta' => '.xlsx or .csv',

            'step_3_title' => 'Upload and get paid',
            'step_3_body' => 'Send the file back for review. Approved work credits your balance the same day, and you withdraw from $10.',
            'step_3_meta' => 'Whish Money or cash',
        ],

        'tasks' => [
            'eyebrow' => 'Open now',
            'title' => 'Tasks waiting for someone',
            'body' => 'Live from the queue. Rewards run from $0.50 to $2.00 a task.',
            'view_all' => 'See every open task',
            'empty_title' => 'The queue is clear',
            'empty_body' => 'Every task has been claimed. New batches are posted through the day — create an account and we will hold your place.',
            'guest_cta' => 'Sign in to claim',
            'guest_hint' => 'Create a free account to claim this task.',
        ],

        'payout' => [
            'eyebrow' => 'Getting paid',
            'title' => 'Your balance, and how it leaves',
            'body' => 'Every approved task writes a line to your ledger. Request a payout at $10 and it goes to a human for review the same hour.',
            'min' => 'Minimum payout',
            'methods' => 'Payout methods',
            'methods_value' => 'Whish Money · Cash',
            'currency' => 'Paid in',
            'currency_value' => 'US dollars',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */
    'footer' => [
        'blurb' => 'A micro-task platform for turning scanned documents into clean, structured spreadsheets.',
        'navigate' => 'Navigate',
        'account' => 'Account',
        'rights' => 'All rights reserved.',
        'built' => 'Built for careful typists.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'register_title' => 'Create your account',
        'register_body' => 'One account, one balance, one payout method. Takes about a minute.',
        'login_title' => 'Welcome back',
        'login_body' => 'Sign in to claim tasks and check your balance.',
        'forgot_title' => 'Reset your password',
        'forgot_body' => 'Enter the email on your account and we will send a reset link.',
        'reset_title' => 'Choose a new password',
        'reset_body' => 'Pick something you have not used elsewhere.',

        'name' => 'Full name',
        'email' => 'Email address',
        'phone' => 'Phone number',
        'phone_hint' => 'Used for Whish Money payouts. You can change it later.',
        'password' => 'Password',
        'password_confirm' => 'Confirm password',
        'remember' => 'Keep me signed in',
        'forgot_link' => 'Forgot your password?',

        'register_submit' => 'Create account',
        'login_submit' => 'Sign in',
        'forgot_submit' => 'Email reset link',
        'reset_submit' => 'Save new password',

        'have_account' => 'Already have an account?',
        'no_account' => 'New to Sortifya?',
        'back_to_login' => 'Back to sign in',

        'suspended' => 'This account is suspended. Contact support to reopen it.',
        'reset_subject' => 'Reset your Sortifya password',
        'reset_greeting' => 'Hello :name,',
        'reset_line_1' => 'Someone asked to reset the password for your Sortifya account. If that was you, use the button below.',
        'reset_action' => 'Reset password',
        'reset_expiry' => 'The link stops working in :count minutes.',
        'reset_line_2' => 'If you did not ask for this, no action is needed — your password stays as it is.',
        'reset_salutation' => '— The Sortifya team',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    'dashboard' => [
        'greeting' => 'Hello, :name',
        'subtitle' => 'Here is your balance and what is open to claim.',

        'balance' => 'Available balance',
        'balance_hint' => 'Ready to withdraw',
        'pending' => 'Awaiting review',
        'pending_hint' => 'Credits once approved',
        'approved_count' => 'Tasks approved',
        'approved_hint' => 'Lifetime',
        'active_lock' => 'Claimed by you',
        'active_lock_hint' => 'Finish before the hold ends',

        'active_title' => 'On your desk',
        'active_body' => 'These are locked to you. Upload before the hold runs out or they return to the queue.',
        'active_empty' => 'Nothing claimed right now. Pick something from the open list below.',

        'open_title' => 'Open tasks',
        'open_body' => 'First to claim gets the task.',
        'open_empty_title' => 'No open tasks',
        'open_empty_body' => 'The queue is empty. New batches are posted through the day — check back shortly.',

        'recent_title' => 'Your recent submissions',
        'recent_empty' => 'No submissions yet. Your first one will show up here.',
        'view_wallet' => 'Open wallet',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tasks & the workbench
    |--------------------------------------------------------------------------
    */
    'task' => [
        'reward' => 'Reward',
        'claim' => 'Claim task',
        'claimed_by_you' => 'Claimed by you',
        'open_workbench' => 'Open workbench',
        'release' => 'Return to queue',
        'release_confirm' => 'Return this task so someone else can take it? Anything you have typed stays on your computer.',
        'time_left' => 'Time left',
        'expired' => 'Hold expired',
        'posted' => 'Posted :time',

        'workbench' => 'Workbench',
        'back' => 'Back to dashboard',

        'source_title' => 'Source document',
        'source_body' => 'The scanned file to transcribe. Download it and keep it open beside your spreadsheet.',
        'download_pdf' => 'Download PDF',
        'template_title' => 'Column template',
        'template_body' => 'Start from this file so your columns line up with what review expects.',
        'download_template' => 'Download template',
        'no_template' => 'This task has no template. Use the column headings described above.',

        'upload_title' => 'Upload your spreadsheet',
        'upload_body' => 'Drop the finished .xlsx or .csv here. Review usually takes under a day.',
        'dropzone_idle' => 'Drop your file here',
        'dropzone_hint' => 'or browse — .xlsx, .xls or .csv up to 10 MB',
        'dropzone_over' => 'Release to attach',
        'dropzone_change' => 'Choose a different file',
        'dropzone_error_type' => 'That file type will not open. Use .xlsx, .xls or .csv.',
        'dropzone_error_size' => 'That file is over 10 MB. Split it or save it as .csv.',
        'submit' => 'Submit for review',
        'submit_hint' => 'You can submit once. Check your columns before sending.',

        'claim_success' => 'Task claimed. You have :minutes minutes.',
        'claim_taken' => 'Someone claimed that task first. Here is what is still open.',
        'claim_limit' => 'Finish or return your current task before claiming another.',
        'released' => 'Task returned to the queue.',
        'submitted' => 'Submitted. We will review it and credit your balance.',
        'expired_notice' => 'The hold on this task ran out and it went back to the queue.',
        'not_yours' => 'That task is not claimed by you.',

        'submission_status' => 'Review status',
        'submitted_at' => 'Submitted :time',
        'rejection_reason' => 'Why it was returned',
        'resubmit' => 'Fix and resubmit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet, ledger & payouts
    |--------------------------------------------------------------------------
    */
    'wallet' => [
        'title' => 'Wallet',
        'subtitle' => 'Every credit and debit on your account, in order.',

        'available' => 'Available balance',
        'lifetime' => 'Earned all time',
        'withdrawn' => 'Paid out',
        'pending_payout' => 'Payout in review',

        'ledger_title' => 'Ledger',
        'ledger_body' => 'Newest first.',
        'ledger_empty_title' => 'Nothing here yet',
        'ledger_empty_body' => 'Approve your first task and the credit lands here.',
        'col_date' => 'Date',
        'col_description' => 'Description',
        'col_type' => 'Type',
        'col_amount' => 'Amount',

        'type' => [
            'task_reward' => 'Task reward',
            'withdrawal' => 'Withdrawal',
            'refund' => 'Refund',
            'bonus' => 'Bonus',
        ],

        'withdraw_title' => 'Request a payout',
        'withdraw_body' => 'Minimum :min. Requests go to a human for review, usually within the hour.',
        'amount' => 'Amount in USD',
        'amount_hint' => 'Up to :max available.',
        'method' => 'Payout method',
        'method_whish' => 'Whish Money',
        'method_cash' => 'Cash',
        'method_bank' => 'Bank transfer',
        'method_other' => 'Other',
        'payout_name' => 'Full name on the account',
        'payout_phone' => 'Phone number',
        'payout_note' => 'Anything we should know',
        'payout_note_hint' => 'Optional — a branch, a pickup time, an account reference.',
        'withdraw_submit' => 'Request payout',

        'below_min' => 'Payouts start at :min. You have :balance.',
        'insufficient' => 'That is more than your available balance of :balance.',
        'requested' => 'Payout requested. We will message you when it is sent.',
        'has_pending' => 'You already have a payout in review. It will clear before you can request another.',

        'history_title' => 'Payout history',
        'history_empty' => 'No payouts requested yet.',
        'col_method' => 'Method',
        'col_status' => 'Status',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared vocabulary
    |--------------------------------------------------------------------------
    */
    'status' => [
        'available' => 'Open',
        'assigned' => 'Claimed',
        'completed' => 'Completed',
        'archived' => 'Archived',
        'pending' => 'In review',
        'approved' => 'Approved',
        'rejected' => 'Returned',
    ],

    'common' => [
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
        'save' => 'Save',
        'close' => 'Close',
        'back' => 'Back',
        'search' => 'Search',
        'none' => '—',
        'required' => 'Required',
        'optional' => 'Optional',
        'loading' => 'Loading',
        'copyright' => '© :year Sortifya.',
    ],

    'profile' => [
        'title' => 'Your profile',
        'subtitle' => 'Your name, contact details, and password.',
        'details' => 'Account details',
        'details_body' => 'The phone number here is what payouts are sent to.',
        'saved' => 'Profile saved.',
        'password_title' => 'Change password',
        'password_body' => 'Enter your current password, then the new one.',
        'current_password' => 'Current password',
        'new_password' => 'New password',
        'password_saved' => 'Password changed.',
    ],
];
