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
        Schema::create('transaction_db', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->foreignId('item_id')->constrained('items_db');
            $table->string('item_name');
            $table->string('ship_to');
            $table->string('ship_from');
            $table->string('approved_at')->nullable();
            $table->string('shipped_at')->nullable();
            $table->string('image_link');
            $table->string('courier_name');
            $table->string('delivered_at')->nullable();
            $table->foreignId('registered_by')->constrained('users');
            $table->string('transaction_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_db');
    }
};
