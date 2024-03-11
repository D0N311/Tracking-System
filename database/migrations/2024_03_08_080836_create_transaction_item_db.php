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
        Schema::create('transaction_item_db', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaction_db');
            $table->foreignId('item_id')->constrained('items_db');
            $table->char('status_id', 10);
            $table->foreign('status_id')->references('id')->on('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_item_db');
    }
};
