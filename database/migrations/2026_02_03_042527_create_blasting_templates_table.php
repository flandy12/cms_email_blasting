<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blasting_templates', function (Blueprint $table) {
            $table->id();

            // Main content
            $table->string('name', 100);
            $table->string('subject', 150);
            $table->longText('wording');

            // Button config
            $table->enum('button_type', ['none', 'button', 'wa'])
                ->default('none');

            $table->string('button_text', 50)
                ->nullable();

            // URL config
            $table->enum('url_type', ['static', 'dynamic'])
                ->nullable();

            $table->text('url')->nullable();

            // Dynamic params
            $table->json('params')->nullable();

            // Status & audit
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Indexes (performance)
            $table->index(['is_active']);
            $table->index(['button_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blasting_templates');
    }
};
