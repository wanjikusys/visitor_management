<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('year')->nullable();
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck', 'van', 'suv', 'bus', 'bicycle', 'other'])->default('car');
            $table->string('photo_path')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->timestamp('blacklisted_at')->nullable();
            $table->timestamps();
            
            $table->index('plate_number');
            $table->index('is_blacklisted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
