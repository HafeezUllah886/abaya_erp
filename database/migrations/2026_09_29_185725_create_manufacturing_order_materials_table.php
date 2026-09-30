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
        Schema::create('manufacturing_order_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // The Raw Material product
            $table->decimal('own_qty', 10, 2)->default(0); // From own stock
            $table->decimal('customer_qty', 10, 2)->default(0); // From customer
            $table->decimal('cost_price', 15, 2)->default(0); // Optional: if you want to store the average purchase price at the time of creation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_order_materials');
    }
};
