<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carers', function (Blueprint $table) {
            $table->id();
            $table->string('baby_admitted'); // OccupancyID
            $table->string('patient_name');
            $table->string('patient_number', 50);
            $table->string('ward', 50);
            $table->string('bed_number', 50);
            $table->string('carer_name');
            $table->string('carer_contact', 20);
            $table->string('carer_id_number', 50)->nullable();
            $table->string('relationship', 50)->nullable();
            $table->text('notes')->nullable();
            $table->datetime('date_in');
            $table->datetime('date_out')->nullable();
            $table->unsignedBigInteger('registered_by')->nullable();
            $table->timestamps();

            $table->foreign('registered_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carers');
    }
};
