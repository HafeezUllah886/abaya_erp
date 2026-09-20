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
        Schema::create('issue_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_voucher_id')->constrained('issue_vouchers')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->float('qty');
            $table->decimal('cost_at_issue', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_voucher_details');
    }
};
