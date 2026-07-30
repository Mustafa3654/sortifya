<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Titles and bodies are authored in both languages up front so a
            // task never appears half-translated to an Arabic worker.
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('description_en');
            $table->text('description_ar');

            $table->string('pdf_file_path');
            $table->string('sample_template_path')->nullable();

            $table->decimal('reward_usd', 8, 2);

            $table->enum('status', ['available', 'assigned', 'completed', 'archived'])
                ->default('available');

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // The public queue reads "available, newest first".
            $table->index(['status', 'created_at']);
            // The dashboard reads "what does this user hold right now".
            $table->index(['assigned_to_user_id', 'status']);
            // tasks:release-expired sweeps on this pair every five minutes.
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
