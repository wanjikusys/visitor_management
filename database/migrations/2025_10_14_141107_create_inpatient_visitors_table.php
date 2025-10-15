<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inpatient_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('patient_number', 50);
            $table->string('patient_name');
            $table->string('visitor_name');
            $table->string('visitor_contact', 20);
            $table->string('relationship', 100);
            $table->datetime('check_in_time');
            $table->datetime('check_out_time')->nullable();
            $table->text('purpose_of_visit')->nullable();
            $table->unsignedBigInteger('checked_in_by')->nullable();
            $table->unsignedBigInteger('checked_out_by')->nullable();
            $table->timestamps();

            $table->index('patient_number');
            $table->index('check_in_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inpatient_visitors');
    }
};
