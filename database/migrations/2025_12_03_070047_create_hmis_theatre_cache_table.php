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
        Schema::create('hmis_theatre_cache', function (Blueprint $table) {
            $table->id();
            
            // Core Theatre Request Data
            // NOTE: Length limited to 100 to fix MySQL 'Specified key was too long' error (1071) when indexing.
            $table->string('PatientNumber', 100)->index(); 
            $table->string('PatientName');
            $table->string('Gender')->nullable();
            $table->string('NOKName')->nullable();
            $table->dateTime('SessionDate')->index();
            $table->string('OperationRoom')->nullable();
            $table->string('SessionType')->nullable();
            $table->string('Consultant')->nullable();
            
            // NOTE: Length limited to 50 to fix MySQL 'Specified key was too long' error (1071) when indexing.
            $table->string('Status', 50)->index(); 
            
            // Mapped from TheatreDayCase
            $table->boolean('IsDayCase')->default(false); 

            // Sync Information
            $table->timestamp('cached_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hmis_theatre_cache');
    }
};