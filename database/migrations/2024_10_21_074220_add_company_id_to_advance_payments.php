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
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable(); // or remove nullable() if it's required

            $table->foreign('company_id')->references('id')->on('admins')
                  ->onDelete('cascade'); // Optional: specify what happens on delete

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
