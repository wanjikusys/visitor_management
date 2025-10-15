<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_visit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who logged this
            $table->enum('event_type', ['entry', 'exit', 'moved', 'flagged', 'inspection'])->default('entry');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->string('camera_id')->nullable(); // For CCTV integration
            $table->string('photo_path')->nullable();
            $table->timestamp('event_time');
            $table->timestamps();
            
            $table->index('event_time');
            $table->index(['vehicle_visit_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_tracking_logs');
    }
};
