<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $batch = $this->faker->numberBetween(100, 999);
        $rows = $this->faker->numberBetween(18, 120);

        return [
            'title_en' => "Invoice batch {$batch} — {$rows} rows",
            'title_ar' => "دفعة فواتير {$batch} — {$rows} صفاً",
            'description_en' => 'Transcribe each line into the columns Date, Vendor, Description, Amount. Keep amounts as plain numbers with two decimals.',
            'description_ar' => 'أدخل كل سطر في الأعمدة: التاريخ، المورّد، البيان، المبلغ. اكتب المبالغ أرقاماً مجرّدة بخانتين عشريتين.',
            'pdf_file_path' => 'tasks/sources/sample-batch.pdf',
            'sample_template_path' => null,
            // The advertised band on the landing page is $0.50–$2.00.
            'reward_usd' => $this->faker->randomElement([0.50, 0.75, 1.00, 1.25, 1.50, 2.00]),
            'status' => TaskStatus::Available,
        ];
    }

    public function assignedTo(int $userId): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Assigned,
            'assigned_to_user_id' => $userId,
            'assigned_at' => now(),
            'expires_at' => now()->addMinutes((int) config('sortifya.task_hold_minutes')),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::Completed]);
    }
}
