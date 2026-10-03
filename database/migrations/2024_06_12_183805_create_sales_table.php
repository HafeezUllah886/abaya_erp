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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('accounts');
            $table->date('date');
            $table->float('total')->default(0);
            $table->text('notes')->nullable();
            $table->date('delivery_date')->nullable();
            $table->float('vat')->nullable();
            $table->float('vat_amount')->nullable();
            $table->float('total_bill')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('contact')->nullable();
            $table->bigInteger('refID');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
