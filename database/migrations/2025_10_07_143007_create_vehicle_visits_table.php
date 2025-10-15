<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('visitor_visit_id')->constrained()->onDelete('cascade');
            $table->foreignId('parking_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('parking_slot')->nullable();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('entry_notes')->nullable();
            $table->text('exit_notes')->nullable();
            $table->enum('status', ['parked', 'exited'])->default('parked');
            $table->timestamps();
            
            $table->index('entry_time');
            $table->index(['parking_zone_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_visits');
    }
};
