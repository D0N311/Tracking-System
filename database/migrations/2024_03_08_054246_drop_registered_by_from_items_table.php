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
        Schema::table('items_db', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['registered_by']);
            // Drop the column
            $table->dropColumn('registered_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_db', function (Blueprint $table) {
            // Add the column back
            $table->unsignedBigInteger('registered_by')->nullable();

            // Add the foreign key constraint back
            $table->foreign('registered_by')->references('id')->on('users');
        });
    }
};
