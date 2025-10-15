<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->string('visit_purpose');
            $table->text('visit_notes')->nullable();
            $table->string('badge_number')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->timestamp('check_in_time');
            $table->timestamp('expected_checkout_time')->nullable();
            $table->timestamp('actual_checkout_time')->nullable();
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled', 'no_show'])->default('active');
            $table->string('temperature')->nullable(); // For health screening
            $table->boolean('health_screening_passed')->default(true);
            $table->text('checkout_notes')->nullable();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['status', 'check_in_time']);
            $table->index('check_in_time');
            $table->index('host_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
