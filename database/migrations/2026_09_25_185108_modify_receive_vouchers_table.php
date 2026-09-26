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
        Schema::table('receive_vouchers', function (Blueprint $table) {
            $table->foreignId('tailor_id')->nullable()->constrained('accounts');
            $table->enum('status', ['Received'])->default('Received')->nullable();
            
            // Make issue_voucher_id nullable
            $table->unsignedBigInteger('issue_voucher_id')->nullable()->change();
            // Make refID nullable just in case it's missed
            $table->bigInteger('refID')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receive_vouchers', function (Blueprint $table) {
            $table->dropForeign(['tailor_id']);
            $table->dropColumn(['tailor_id', 'status']);
        });
    }
};
