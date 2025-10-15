<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('total_slots');
            $table->integer('available_slots');
            $table->enum('zone_type', ['visitor', 'vip', 'staff', 'loading', 'disabled', 'motorcycle'])->default('visitor');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['zone_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_zones');
    }
};
