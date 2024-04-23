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
        Schema::table('transaction_db', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->dropColumn('item_name');

            $table->timestamp('r_description')->nullable()->after('description');
            $table->timestamp('r_image_link')->nullable()->after('image_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_db', function (Blueprint $table) {
            //
        });
    }
};
