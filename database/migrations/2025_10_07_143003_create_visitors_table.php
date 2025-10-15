<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('id_number')->unique();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('id_type', ['national_id', 'passport', 'driving_license', 'other'])->default('national_id');
            $table->text('address')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->timestamp('blacklisted_at')->nullable();
            $table->timestamps();
            
            $table->index('id_number');
            $table->index('phone_number');
            $table->index('is_blacklisted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
