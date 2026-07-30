<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Always stored positive. The matching ledger line carries the sign.
            $table->decimal('amount', 8, 2);

            $table->enum('method', ['whish_money', 'cash', 'bank_transfer', 'other']);

            // Shape varies by method: a Whish payout needs a phone and a name,
            // a cash pickup needs a place. Keeping it JSON avoids four nullable
            // columns that are empty three times out of four.
            $table->json('payout_details');

            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])
                ->default('pending');

            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
