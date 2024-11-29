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
            // $table->string('address')->nullable();
        $table->string('city')->nullable();
        $table->string('distt')->nullable();
        $table->string('state')->nullable();
        $table->string('gst_no')->nullable();
        $table->string('pan_no')->nullable();
        $table->string('aadhar_no')->nullable();
        $table->string('udyam_no')->nullable();
        $table->string('cin_no')->nullable();
        $table->string('epf_no')->nullable();
        $table->string('esic_no')->nullable();
        $table->string('bank_name')->nullable();
        $table->string('ac_no')->nullable();
        $table->string('ifs_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_details', function (Blueprint $table) {
            $table->dropColumn([
                'city', 'distt', 'state', 'gst_no', 'pan_no', 'aadhar_no', 
                'udyam_no', 'cin_no', 'epf_no', 'esic_no', 'bank_name', 'ac_no', 'ifs_code'
            ]);
        });
    }
};
