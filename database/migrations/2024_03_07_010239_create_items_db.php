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
        Schema::create('items_db', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('stock')->nullable();
            $table->string('model_number')->nullable();
            $table->string('image_link')->nullable();
            $table->string('item_status')->nullable();
            $table->string('confirmed_at')->nullable();
            $table->foreignId('under_company')->nullable()->constrained('company_db');
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->foreignId('owned_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_db');
    }
};
