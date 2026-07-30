<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The ledger is the single source of truth for a balance. There is no
     * cached total on `users` — a balance is always SUM(amount) for a user,
     * so a row can never disagree with the number shown to them.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Signed: credits are positive, debits negative. Never abs().
            $table->decimal('amount', 8, 2);

            $table->enum('type', ['task_reward', 'withdrawal', 'refund', 'bonus']);
            $table->string('description');

            // Points back at whatever caused the line — a Submission for a
            // reward, a Withdrawal for a debit or its refund.
            $table->nullableMorphs('reference');

            $table->timestamps();

            // Balance lookups and the wallet ledger both read on this.
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
