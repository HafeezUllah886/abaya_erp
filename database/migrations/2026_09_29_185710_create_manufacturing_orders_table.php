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
        Schema::create('manufacturing_orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('tailor_id')->constrained('accounts')->onDelete('cascade');
            $table->enum('payment_type', ['Paid', 'Unpaid']);
            $table->foreignId('business_account_id')->nullable()->constrained('accounts')->onDelete('set null'); // if Paid
            $table->decimal('total_amount', 15, 2)->default(0); // Optional: Cost or payment amount
            $table->text('notes')->nullable();
            $table->bigInteger('refID')->nullable(); // if you use refs for transactions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_orders');
    }
};
