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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'hours'])->default('present');
            $table->integer('hours')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
