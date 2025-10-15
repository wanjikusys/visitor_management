<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hmis_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('card_no', 50);
            $table->string('driver_name');
            $table->string('registration', 50);
            $table->string('phone_number', 20);
            $table->string('visit_purpose');
            $table->integer('passengers')->default(1);
            $table->datetime('time_in');
            $table->datetime('time_out')->nullable();
            $table->boolean('checked_in')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('checked_in_by')->nullable();
            $table->unsignedBigInteger('checked_out_by')->nullable();
            $table->timestamps();

            $table->index('checked_in');
            $table->index('card_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hmis_vehicles');
    }
};
