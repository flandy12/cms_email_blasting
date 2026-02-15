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
        Schema::create('blasting_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('blasting_templates');
            $table->string('name');
            $table->dateTime('scheduled_at')->nullable();
            $table->enum('status', ['draft', 'running', 'paused', 'finished'])->default('draft');
            $table->integer('total_recipient')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blasting_campaigns');
    }
};
