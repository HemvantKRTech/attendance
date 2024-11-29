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
        Schema::table('admin_details', function (Blueprint $table) {
            $table->string('employee_code')->nullable();  // Add Employee Code
            $table->string('designation')->nullable();     // Add Designation
            $table->string('department')->nullable();      // Add Department
            $table->enum('employment_type', ['Permanent', 'Daily Wages', 'Contract'])->nullable(); // Add Employment Type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_details', function (Blueprint $table) {
            $table->dropColumn(['employee_code', 'designation', 'department', 'employment_type']);
        });
    }
};
