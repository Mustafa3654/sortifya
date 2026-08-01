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
        'faq' => 'FAQ',
        'contact' => 'Contact',
        'terms' => 'Terms',
        'privacy' => 'Privacy',
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
            'title_lead' => 'Turn unstructured data into',
            // "clean Excel data" would repeat "data" from the lead line; the
            // headline says sheets instead, and keeps Excel.
            'title_accent' => 'clean Excel sheets',
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
        'support' => 'Support',
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

    /*
    |--------------------------------------------------------------------------
    | Supporting pages
    |--------------------------------------------------------------------------
    |
    | The terms and privacy copy below describes how this platform actually
    | behaves — hold times, the review loop, the payout floor, what is stored
    | and for how long. It is written to be accurate, not to be legal advice.
    | Have a lawyer in your jurisdiction read it before you rely on it, and
    | fill in config('sortifya.company') so it names a real entity.
    |
    */

    'pages' => [

        'faq' => [
            'eyebrow' => 'Questions',
            'title' => 'Questions people ask before starting',
            'body' => 'If your question is not here, write to us — a person answers.',
            'still_stuck' => 'Still not sure about something?',
            'contact_cta' => 'Ask us directly',

            'groups' => [
                [
                    'heading' => 'Getting started',
                    'items' => [
                        [
                            'q' => 'What is Sortifya, in one sentence?',
                            'a' => 'You take a scanned document, type what it says into a spreadsheet, upload the spreadsheet, and get paid once someone checks it.',
                        ],
                        [
                            'q' => 'What do I need to start?',
                            'a' => "A computer and any spreadsheet program — Excel, Numbers, or the free Google Sheets. Nothing else, and there is nothing to install.\n\nA phone technically works but is slow going; these tasks involve a lot of typing.",
                        ],
                        [
                            'q' => 'Does it cost anything to join?',
                            'a' => 'No. Signing up is free, and we never ask you to pay for tasks, training, or withdrawals. If anyone asks you to pay to work here, it is not us.',
                        ],
                        [
                            'q' => 'Do I need experience?',
                            'a' => 'No. If you can read a document and type what it says into the right column, you can do every task on the platform. Most tasks include a template so the columns are already laid out for you.',
                        ],
                    ],
                ],
                [
                    'heading' => 'Doing the work',
                    'items' => [
                        [
                            'q' => 'How long do I have once I claim a task?',
                            'a' => 'Forty-five minutes. The task is locked to you for that time so nobody else can take it while you work. The countdown is on screen the whole time.',
                        ],
                        [
                            'q' => 'What if I cannot finish in time?',
                            'a' => "The task returns to the open list and someone else can claim it. Nothing is held against you, and you can claim it again if it is still there.\n\nIf you know you will not finish, use “Return to queue” so it goes back straight away.",
                        ],
                        [
                            'q' => 'Can I work on several tasks at once?',
                            'a' => 'No — one at a time. It keeps the queue moving and stops tasks being locked up by someone who is not working on them.',
                        ],
                        [
                            'q' => 'What file should I upload?',
                            'a' => 'An .xlsx, .xls, or .csv file, up to 10 MB. If the task came with a template, fill in that file and send it back.',
                        ],
                        [
                            'q' => 'Something in the document is unreadable. What do I do?',
                            'a' => 'Leave that cell empty rather than guessing. A blank cell is easy to fix later; a wrong number that looks right is not, and guessing is the most common reason work gets returned.',
                        ],
                    ],
                ],
                [
                    'heading' => 'Getting paid',
                    'items' => [
                        [
                            'q' => 'How much can I earn?',
                            'a' => "Each task pays between \$0.50 and \$2.00, shown on the task before you claim it. What you earn in a day depends on how many tasks are posted and how quickly you work.\n\nWe would rather be straight with you than quote a number we cannot promise: this is paid piece work, not a salary.",
                        ],
                        [
                            'q' => 'When does the money reach my balance?',
                            'a' => 'When your upload is approved. Review usually takes under a day. Until then it shows on your dashboard as awaiting review.',
                        ],
                        [
                            'q' => 'When can I withdraw?',
                            'a' => 'Once your balance reaches $10.00. You can leave it to build up past that if you prefer.',
                        ],
                        [
                            'q' => 'How is the money sent?',
                            'a' => 'Whish Money or cash. You choose when you request the payout and enter the name and number it should go to.',
                        ],
                        [
                            'q' => 'Why was my work returned?',
                            'a' => "Whoever reviewed it writes the reason, and you see it word for word on your dashboard. Usually it is a missing column, guessed values, or amounts typed with currency symbols when the task asked for plain numbers.\n\nA returned task comes back to you to fix. Nothing is deducted — the reward simply is not paid until it passes.",
                        ],
                        [
                            'q' => 'A payout was declined. Where is my money?',
                            'a' => 'Back on your balance. A declined payout is refunded in full and both the original request and the refund stay visible in your wallet, so you can always see what happened.',
                        ],
                    ],
                ],
                [
                    'heading' => 'Account and privacy',
                    'items' => [
                        [
                            'q' => 'Who can see the files I upload?',
                            'a' => 'Only you and the people who review submissions. Uploads are stored outside the public part of the site and cannot be reached by a link.',
                        ],
                        [
                            'q' => 'Can I change my phone number or email?',
                            'a' => 'Yes, on your profile page at any time. Keep the phone number current — it is where Whish Money payouts are sent.',
                        ],
                        [
                            'q' => 'Can I delete my account?',
                            'a' => 'Write to us and we will close it. Withdraw any remaining balance first, and note that we keep payment records for as long as the law requires even after an account is closed.',
                        ],
                    ],
                ],
            ],
        ],

        'terms' => [
            'eyebrow' => 'Legal',
            'title' => 'Terms of service',
            'updated' => 'Last updated :date',
            'intro' => 'These terms describe how Sortifya works, what we owe you, and what we ask of you. Plain language on purpose — if something here is unclear, ask us and we will explain it.',
            'toc' => 'On this page',

            'sections' => [
                [
                    'heading' => 'Who we are',
                    'body' => "Sortifya is operated by :company, based in :country. You can reach us at :email.\n\nUsing the platform means you accept these terms. If you do not accept them, do not use it.",
                ],
                [
                    'heading' => 'What Sortifya does',
                    'body' => "We publish documents that need transcribing into spreadsheets. You may claim one, do the work on your own computer, and upload the result. If it is accepted, we credit your balance in US dollars and you can withdraw once you reach the minimum.\n\nYou work for yourself. Nothing here creates employment, and we do not guarantee that any work will be available at a given time.",
                ],
                [
                    'heading' => 'Your account',
                    'body' => "You need an account to claim tasks. Give accurate details and keep them current — payouts go to the phone number on your account.\n\nOne account per person. Keep your password to yourself; anything done through your account is treated as done by you. Tell us straight away if you think someone else has access.\n\nYou must be old enough to enter a contract where you live.",
                ],
                [
                    'heading' => 'Claiming and completing tasks',
                    'body' => "Claiming a task locks it to you for the period shown on screen, currently :hold minutes. When that time passes the task returns to the queue whether or not you have finished, and anyone may claim it.\n\nYou may hold :max task at a time. Do the work yourself and do not share task documents with anyone else — they often contain other people's information.",
                ],
                [
                    'heading' => 'Review, approval, and rework',
                    'body' => "Every upload is reviewed by a person. If it is accurate, we approve it and credit the reward shown on the task at the time you claimed it.\n\nIf it is not, we return it with a written reason and you may correct and resubmit. We do not charge you for returned work and nothing is deducted from your balance — the reward is simply not paid until the work passes.\n\nWe decide whether work meets the standard, and we will always tell you why.",
                ],
                [
                    'heading' => 'Balance and payouts',
                    'body' => "Your balance is the running total of everything credited to you, less anything paid out. Every movement is recorded in your wallet and you can see the full history at any time.\n\nYou may request a payout once your balance reaches :minimum. Choose Whish Money or cash and give the name and number it should go to. The amount leaves your available balance as soon as you request it, so it cannot be spent twice.\n\nA person reviews every payout. If we decline one, we refund the full amount to your balance and tell you why. We are not responsible for money that cannot be delivered because you gave us the wrong details.\n\nBalances are a record of what we owe you for accepted work. They are not a bank deposit, they earn no interest, and they cannot be transferred to another person.",
                ],
                [
                    'heading' => 'What you must not do',
                    'body' => "Do not submit work you did not do. Do not use automated tools to claim tasks or generate submissions. Do not open more than one account, and do not share, publish, or keep the contents of task documents once your work is submitted.\n\nDo not attempt to reach parts of the platform that are not meant for you, and do not interfere with its operation.",
                ],
                [
                    'heading' => 'The work you produce',
                    'body' => "The spreadsheets you produce from our documents belong to us or to our client once accepted, and you agree to that transfer as part of being paid for the work.\n\nThe source documents remain the property of whoever owns them. You are given access only to complete the task.",
                ],
                [
                    'heading' => 'Suspending or closing an account',
                    'body' => "We may suspend an account that breaks these terms. Where we do, we will say why.\n\nIf we suspend an account for fraud — work you did not do, duplicate accounts, automated submissions — we may withhold the balance connected to that activity. Otherwise, any balance already earned on accepted work remains yours to withdraw.\n\nYou may ask us to close your account at any time. Withdraw your balance first.",
                ],
                [
                    'heading' => 'What we do not promise',
                    'body' => "We provide the platform as it is. We do not promise that tasks will always be available, that the site will never be down, or that review will always take a particular length of time.\n\nNothing here limits liability that cannot be limited by law. Beyond that, our responsibility to you is limited to the balance owed on your accepted work.",
                ],
                [
                    'heading' => 'Changes to these terms',
                    'body' => "We may update these terms. When we make a change that affects you materially, we will say so on the platform before it takes effect. Continuing to use Sortifya after that means you accept the updated terms.",
                ],
                [
                    'heading' => 'Governing law',
                    'body' => "These terms are governed by the laws of :country, and disputes fall to its courts.",
                ],
            ],
        ],

        'privacy' => [
            'eyebrow' => 'Legal',
            'title' => 'Privacy',
            'updated' => 'Last updated :date',
            'intro' => 'What we collect, why we need it, and what we do not do with it. We collect as little as the platform can work with.',
            'toc' => 'On this page',

            'sections' => [
                [
                    'heading' => 'Who is responsible',
                    'body' => ":company, based in :country, decides how the information described here is handled. For anything about your data, write to :email.",
                ],
                [
                    'heading' => 'What we collect',
                    'body' => "**When you register:** your name, email address, and — if you give it — a phone number.\n\n**When you work:** which tasks you claim and when, the spreadsheets you upload, and the outcome of each review.\n\n**When you are paid:** the amount, the method, and the name and number you asked us to send it to.\n\n**When you write to us:** your name, email, message, and the address your request came from, so we can answer and so we can deal with abuse of the form.\n\n**Automatically:** a session cookie that keeps you signed in and a cookie remembering your language. We do not use advertising or tracking cookies, and there is no third-party analytics on this site.",
                ],
                [
                    'heading' => 'Why we hold it',
                    'body' => "To run your account, hand out and track work, review submissions, calculate what you are owed, pay you, answer your questions, and keep the financial records the law requires us to keep.\n\nWe do not build profiles, and we do not use your information to advertise to you.",
                ],
                [
                    'heading' => 'The files you upload',
                    'body' => "Uploaded spreadsheets are stored outside the public part of the site. They cannot be reached by guessing a link — every download passes through a check that you are either the person who uploaded it or someone reviewing it.\n\nThe first rows of each upload are copied into our review screen so a reviewer can check the work without opening the file.",
                ],
                [
                    'heading' => 'Who else sees it',
                    'body' => "Only where the platform cannot work otherwise:\n\n**Our email provider** delivers password resets and replies. They see your email address and the content of those messages.\n\n**Telegram** receives a payout alert — your name, the amount, and the payout details you entered — so an administrator can approve it. This runs only if the operator has enabled it.\n\n**Our hosting provider** stores the database and files.\n\nWe do not sell your information, and we do not share it for anyone else's marketing.",
                ],
                [
                    'heading' => 'How long we keep it',
                    'body' => "Your account and its history stay while the account is open.\n\nUploaded spreadsheets are kept while the related task and payment records are active.\n\nPayment records are kept for as long as accounting and tax law requires, which is usually several years and continues after an account is closed.\n\nContact messages are kept while we are dealing with them and for a reasonable period afterwards.",
                ],
                [
                    'heading' => 'Your choices',
                    'body' => "You can see and change your name, email, and phone number on your profile at any time, and your full payment history is always visible in your wallet.\n\nWrite to us to ask for a copy of your information, to correct something, or to close your account. Depending on where you live you may have further rights over your data; tell us what you need and we will do what the law requires.\n\nWe cannot delete records we are legally required to keep, and we will say so plainly if that applies.",
                ],
                [
                    'heading' => 'Security',
                    'body' => "Passwords are stored hashed and cannot be read by us or recovered — a reset creates a new one. Uploads sit outside the web root and are served only through an access check. Sessions are signed.\n\nNo system is perfectly secure. If a breach affects you, we will tell you.",
                ],
                [
                    'heading' => 'Children',
                    'body' => 'Sortifya is not for children. Do not register if you are not old enough to enter a contract where you live.',
                ],
                [
                    'heading' => 'Changes',
                    'body' => 'If we change how we handle your information, we will update this page and change the date at the top.',
                ],
            ],
        ],

        'contact' => [
            'eyebrow' => 'Contact',
            'title' => 'Talk to a person',
            'body' => 'Questions about a task, a payout that has not arrived, or anything else. We read every message.',

            'form_title' => 'Send a message',
            'name' => 'Your name',
            'email' => 'Your email',
            'email_hint' => 'We reply to this address.',
            'subject' => 'What is it about',
            'subject_placeholder' => 'A payout that has not arrived',
            'message' => 'Your message',
            'message_placeholder' => 'Tell us what happened, and include the task or payout number if you have it.',
            'submit' => 'Send message',

            'sent' => 'Message sent. We will reply to the address you gave us.',
            'stored_not_sent' => 'Your message was saved and we will see it, but our mail system did not confirm delivery. If it is urgent, please email us directly.',

            'direct_title' => 'Or reach us directly',
            'email_label' => 'Email',
            'phone_label' => 'Phone',
            'response_title' => 'When to expect a reply',
            'response_body' => 'Within :hours hours on working days. Payout problems go to the front of the queue.',

            'before_title' => 'Before you write',
            'before_body' => 'A lot of questions are already answered, and you will get an instant answer there instead of waiting.',
            'before_cta' => 'Read the FAQ',

            'signed_in_as' => 'Writing as :name',
        ],
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
