<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contact messages are stored as well as emailed.
     *
     * Mail is the notification, not the record. If Brevo is misconfigured or
     * briefly down, an emailed-only message is gone — and on a platform where
     * people write in about missing payouts, that is the worst thing to lose.
     */
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            // Null for a visitor who is not signed in.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');

            $table->enum('status', ['new', 'handled'])->default('new');

            // Did the notification actually leave? A false here with a row
            // present is exactly the case this table exists to catch.
            $table->boolean('was_emailed')->default(false);

            $table->string('ip_address', 45)->nullable();
            $table->string('locale', 5)->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
