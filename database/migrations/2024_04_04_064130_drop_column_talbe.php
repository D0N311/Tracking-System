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
            $table->dropForeign(['item_id']); // Drop the foreign key constraint
            $table->dropColumn('item_id');
        });
    }
    
    public function down(): void
    {
        Schema::table('transaction_db', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->after('transaction_id'); // Add the column back
            $table->foreign('item_id')->references('id')->on('items_db'); // Add the foreign key constraint back
        });
    }
};
