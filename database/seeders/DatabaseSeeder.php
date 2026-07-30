<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use App\Services\WalletService;
use Database\Seeders\Support\PlaceholderFiles;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $wallet = app(WalletService::class);

        /*
        |----------------------------------------------------------------------
        | Admin
        |----------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@sortifya.com'],
            [
                'name' => 'Sortifya Admin',
                'password' => 'password123',
                'phone_number' => '+961 70 111 222',
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        /*
        |----------------------------------------------------------------------
        | Workers
        |----------------------------------------------------------------------
        |
        | Two accounts in deliberately different states: one above the $10
        | payout floor and one below it, so both branches of the wallet screen
        | can be seen without editing data by hand.
        |
        */

        $rania = User::updateOrCreate(
            ['email' => 'rania@example.com'],
            [
                'name' => 'Rania Haddad',
                'password' => 'password123',
                'phone_number' => '+961 71 445 908',
                'role' => UserRole::User,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $karim = User::updateOrCreate(
            ['email' => 'karim@example.com'],
            [
                'name' => 'Karim Btaddini',
                'password' => 'password123',
                'phone_number' => '+961 76 220 314',
                'role' => UserRole::User,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        // Guarded: re-running the seeder must not inflate a balance.
        if ($rania->transactions()->doesntExist()) {
            $history = [
                ['Invoice batch 118 — 42 rows', 1.50, 26],
                ['Delivery manifest 07 — 60 rows', 2.00, 21],
                ['Pharmacy stock count — 35 rows', 1.25, 15],
                ['Receipt bundle 231 — 28 rows', 1.00, 11],
                ['Utility ledger Q1 — 90 rows', 2.00, 6],
                ['Payroll sheet 04 — 55 rows', 1.50, 3],
                ['Customs list 88 — 40 rows', 1.25, 1],
                ['Accuracy bonus — March', 2.00, 1],
            ];

            foreach ($history as [$description, $amount, $daysAgo]) {
                $type = str_contains($description, 'bonus')
                    ? TransactionType::Bonus
                    : TransactionType::TaskReward;

                $wallet->credit($rania, $amount, $type, $description)
                    ->forceFill([
                        'created_at' => now()->subDays($daysAgo),
                        'updated_at' => now()->subDays($daysAgo),
                    ])
                    ->save();
            }
        }

        if ($karim->transactions()->doesntExist()) {
            $starter = [
                ['Receipt bundle 194 — 22 rows', 1.00, 9],
                ['Invoice batch 121 — 47 rows', 1.50, 4],
            ];

            foreach ($starter as [$description, $amount, $daysAgo]) {
                $wallet->credit($karim, $amount, TransactionType::TaskReward, $description)
                    ->forceFill([
                        'created_at' => now()->subDays($daysAgo),
                        'updated_at' => now()->subDays($daysAgo),
                    ])
                    ->save();
            }
        }

        /*
        |----------------------------------------------------------------------
        | Tasks
        |----------------------------------------------------------------------
        */

        foreach ($this->taskBlueprints() as $blueprint) {
            $pdf = PlaceholderFiles::pdf(
                config('sortifya.uploads.source_path')."/{$blueprint['slug']}.pdf",
                $blueprint['title_en'],
                $blueprint['lines'],
            );

            $template = PlaceholderFiles::template(
                config('sortifya.uploads.template_path')."/{$blueprint['slug']}-template.csv",
                $blueprint['headers'],
            );

            Task::updateOrCreate(
                ['pdf_file_path' => $pdf],
                [
                    'title_en' => $blueprint['title_en'],
                    'title_ar' => $blueprint['title_ar'],
                    'description_en' => $blueprint['description_en'],
                    'description_ar' => $blueprint['description_ar'],
                    'sample_template_path' => $template,
                    'reward_usd' => $blueprint['reward_usd'],
                    'status' => TaskStatus::Available,
                ],
            );
        }

        $this->command->newLine();
        $this->command->info('Seeded Sortifya.');
        $this->command->table(
            ['Account', 'Email', 'Password'],
            [
                ['Admin', 'admin@sortifya.com', 'password123'],
                ['Worker — above payout floor', 'rania@example.com', 'password123'],
                ['Worker — below payout floor', 'karim@example.com', 'password123'],
            ],
        );
    }

    /**
     * Five demo tasks. The bodies are written the way a real task would be —
     * naming the columns and calling out the one thing people get wrong —
     * because vague sample data hides whether the workbench reads well.
     *
     * @return array<int, array<string, mixed>>
     */
    private function taskBlueprints(): array
    {
        return [
            [
                'slug' => 'invoice-batch-042',
                'title_en' => 'Invoice batch 042 — 38 rows',
                'title_ar' => 'دفعة فواتير ٠٤٢ — ٣٨ صفاً',
                'description_en' => "Scanned supplier invoices from February. Transcribe every line into the columns Date, Vendor, Description, Amount.\n\nWrite amounts as plain numbers with two decimals — no currency symbol, no thousands separator.",
                'description_ar' => "فواتير موردين ممسوحة من شباط. أدخل كل سطر في الأعمدة: التاريخ، المورّد، البيان، المبلغ.\n\nاكتب المبالغ أرقاماً مجرّدة بخانتين عشريتين — بلا رمز عملة وبلا فاصل آلاف.",
                'reward_usd' => 1.50,
                'headers' => ['Date', 'Vendor', 'Description', 'Amount'],
                'lines' => [
                    'Supplier invoices - February 2026',
                    '',
                    '02-14   Nadeem Print Co.      Letterhead, 2000 sheets       148.00',
                    '02-16   Halabi Logistics      Courier, Beirut - Tripoli      92.40',
                    '02-19   Cedar Supply Ltd.     Office chairs x6            1,204.75',
                    '02-23   Mouawad Hardware      Shelving brackets             316.20',
                    '02-27   Beirut Freight        Container handling            540.00',
                    '',
                    '... 33 further rows on the following pages ...',
                ],
            ],
            [
                'slug' => 'delivery-manifest-07',
                'title_en' => 'Delivery manifest 07 — 60 rows',
                'title_ar' => 'كشف توصيل ٠٧ — ٦٠ صفاً',
                'description_en' => "Handwritten delivery manifest. Columns: Order ID, Recipient, District, Weight (kg), Status.\n\nWhere the handwriting is unclear, leave the cell empty rather than guessing.",
                'description_ar' => "كشف توصيل بخط اليد. الأعمدة: رقم الطلب، المستلِم، المنطقة، الوزن (كغ)، الحالة.\n\nحيث يصعب قراءة الخط، اترك الخانة فارغة بدل التخمين.",
                'reward_usd' => 2.00,
                'headers' => ['Order ID', 'Recipient', 'District', 'Weight (kg)', 'Status'],
                'lines' => [
                    'Delivery manifest - week 07',
                    '',
                    'ORD-4412   S. Khoury      Achrafieh    2.4    Delivered',
                    'ORD-4413   M. Aoun        Hamra        1.1    Delivered',
                    'ORD-4414   L. Sassine     Jounieh      7.8    Returned',
                    'ORD-4415   N. Rizk        Zalka        0.6    Delivered',
                    '',
                    '... 56 further rows ...',
                ],
            ],
            [
                'slug' => 'pharmacy-stock-count',
                'title_en' => 'Pharmacy stock count — 35 rows',
                'title_ar' => 'جرد صيدلية — ٣٥ صفاً',
                'description_en' => "Printed stock sheet. Columns: SKU, Product, Batch, Expiry, Units on hand.\n\nWrite expiry dates as YYYY-MM.",
                'description_ar' => "ورقة جرد مطبوعة. الأعمدة: رمز الصنف، المنتج، رقم التشغيلة، الانتهاء، الكمية المتوفرة.\n\nاكتب تواريخ الانتهاء بصيغة YYYY-MM.",
                'reward_usd' => 1.25,
                'headers' => ['SKU', 'Product', 'Batch', 'Expiry', 'Units'],
                'lines' => [
                    'Stock count - branch 3',
                    '',
                    'PH-1180   Paracetamol 500mg    B2291   2027-04    240',
                    'PH-1204   Amoxicillin 250mg    B2310   2026-11     86',
                    'PH-1255   Saline 0.9% 500ml    B2288   2028-01    150',
                    '',
                    '... 32 further rows ...',
                ],
            ],
            [
                'slug' => 'utility-ledger-q1',
                'title_en' => 'Utility ledger Q1 — 90 rows',
                'title_ar' => 'دفتر مرافق الربع الأول — ٩٠ صفاً',
                'description_en' => "Quarterly utility ledger. Columns: Month, Account, Meter reading, Consumption, Charge.\n\nThe last page repeats the header row — do not transcribe it twice.",
                'description_ar' => "دفتر مرافق ربع سنوي. الأعمدة: الشهر، الحساب، قراءة العدّاد، الاستهلاك، القيمة.\n\nالصفحة الأخيرة تكرّر صف العناوين — لا تُدخله مرتين.",
                'reward_usd' => 2.00,
                'headers' => ['Month', 'Account', 'Meter reading', 'Consumption', 'Charge'],
                'lines' => [
                    'Utility ledger - Q1 2026',
                    '',
                    'Jan   AC-0192   48,201   1,204   96.32',
                    'Jan   AC-0193   12,880     640   51.20',
                    'Feb   AC-0192   49,410   1,209   96.72',
                    '',
                    '... 86 further rows ...',
                ],
            ],
            [
                'slug' => 'receipt-bundle-231',
                'title_en' => 'Receipt bundle 231 — 28 rows',
                'title_ar' => 'حزمة إيصالات ٢٣١ — ٢٨ صفاً',
                'description_en' => "Photographed till receipts. Columns: Date, Merchant, Category, Total.\n\nUse the merchant name exactly as printed, including punctuation.",
                'description_ar' => "إيصالات نقاط بيع مصوّرة. الأعمدة: التاريخ، المتجر، الفئة، الإجمالي.\n\nاكتب اسم المتجر كما هو مطبوع تماماً، بعلامات الترقيم نفسها.",
                'reward_usd' => 0.75,
                'headers' => ['Date', 'Merchant', 'Category', 'Total'],
                'lines' => [
                    'Receipt bundle 231',
                    '',
                    '03-02   Spinneys Dbayeh      Groceries      64.15',
                    '03-04   Cafe Younes          Meals           9.00',
                    '03-05   Total Sin el Fil     Fuel           45.00',
                    '',
                    '... 25 further rows ...',
                ],
            ],
        ];
    }
}
