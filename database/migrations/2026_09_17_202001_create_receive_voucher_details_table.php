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
        Schema::create('receive_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receive_voucher_id')->constrained('receive_vouchers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products'); // Abaya finished goods
            $table->float('qty');
            $table->decimal('stitching_cost_per_unit', 10, 2);
            $table->decimal('total_calculated_cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_voucher_details');
    }
};
