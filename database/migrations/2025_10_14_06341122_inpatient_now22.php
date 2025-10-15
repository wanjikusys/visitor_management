<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inpatient_visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('patient_number', 50)->index();
            $table->string('patient_name');
            $table->string('ward_number', 50);
            $table->string('bed_number', 50);
            $table->string('visitor_name');
            $table->string('visitor_id_number', 50)->nullable();
            $table->string('visitor_phone', 20)->nullable();
            $table->string('relationship', 50)->nullable();
            $table->datetime('check_in_time');
            $table->datetime('check_out_time')->nullable();
            $table->unsignedBigInteger('checked_in_by')->nullable();
            $table->unsignedBigInteger('checked_out_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('checked_in_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_out_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpatient_visitor_logs');
    }
};
