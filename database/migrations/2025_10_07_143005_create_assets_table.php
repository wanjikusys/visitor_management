<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_category_id')->constrained()->onDelete('cascade');
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->enum('status', ['available', 'checked_out', 'maintenance', 'retired', 'lost', 'damaged'])->default('available');
            $table->string('location')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('specifications')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('asset_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
