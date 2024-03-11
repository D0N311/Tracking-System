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
        Schema::table('company_db', function (Blueprint $table) {
            $table->string('phone_number')->unique()->nullable();
            $table->string('deactivated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_db', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('deactivated_at');
        });
    }
};
