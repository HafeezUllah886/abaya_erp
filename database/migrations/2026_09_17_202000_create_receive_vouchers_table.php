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
        Schema::create('receive_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_voucher_id')->constrained('issue_vouchers');
            $table->date('date');
            $table->decimal('stitching_charges_total', 10, 2)->default(0);
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receive_vouchers');
    }
};
