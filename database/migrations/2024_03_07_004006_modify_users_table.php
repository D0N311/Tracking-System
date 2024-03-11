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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('role')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('company_db');
            $table->string('activated_at')->nullable();
            $table->string('deactivated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('role');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropColumn('activated_at');
            $table->dropColumn('deactivated_at');
        });
    }
};
