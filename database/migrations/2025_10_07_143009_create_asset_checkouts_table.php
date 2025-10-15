<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('visitor_visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // For staff checkouts
            $table->foreignId('checked_out_by')->constrained('users')->onDelete('cascade'); // Staff who processed checkout
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checkout_time');
            $table->timestamp('expected_return_time');
            $table->timestamp('actual_return_time')->nullable();
            $table->enum('checkout_condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('good');
            $table->enum('return_condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->nullable();
            $table->text('checkout_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('status', ['pending_approval', 'approved', 'checked_out', 'returned', 'overdue', 'lost', 'cancelled'])->default('pending_approval');
            $table->foreignId('returned_by')->nullable()->constrained('users')->nullOnDelete(); // Staff who processed return
            $table->string('checkout_signature')->nullable(); // Path to signature image
            $table->string('return_signature')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'expected_return_time']);
            $table->index('checkout_time');
            $table->index(['asset_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_checkouts');
    }
};
