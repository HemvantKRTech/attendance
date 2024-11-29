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
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('amount', 10, 2);  // Total advance amount
            $table->decimal('emi_amount', 10, 2)->nullable();  // EMI amount (nullable for one-time payments)
            $table->integer('total_emi_count')->nullable();  // Total EMIs for advance (nullable for one-time payments)
            $table->integer('pending_emi_count')->nullable();  // Remaining EMIs (nullable for one-time payments)
            $table->enum('payment_type', ['one-time', 'emi'])->default('emi');  // Type of payment
            $table->enum('status', ['active', 'completed'])->default('active');  // Status of the advance
            $table->date('date_taken');  // Date when advance was taken
            $table->text('description')->nullable();  // Optional description
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('admins')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
