<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hmis_discharge_requests_cache', function (Blueprint $table) {
            // Change OccupancyID from integer to string (to handle values like "176017/25")
            $table->string('OccupancyID', 50)->nullable()->change();
            
            // Add Ward and Bed columns
            $table->string('WardNumber', 50)->nullable()->after('CustomerID');
            $table->string('BedNumber', 50)->nullable()->after('WardNumber');
        });
    }

    public function down(): void
    {
        Schema::table('hmis_discharge_requests_cache', function (Blueprint $table) {
            $table->integer('OccupancyID')->nullable()->change();
            $table->dropColumn(['WardNumber', 'BedNumber']);
        });
    }
};
