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
        //
        Schema::table('supplier_orders', function (Blueprint $table) {
            // Drop the existing status column
            $table->dropColumn('status');

            // Add new date columns
            $table->date('orderPlacedDate')->nullable();
            $table->date('receivedDate')->nullable();
            $table->date('cancelledDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('supplier_order', function (Blueprint $table) {
            // Revert changes: add status back and drop new columns
            $table->enum('status', ['Received', 'Pending', 'Cancelled'])->default('Pending');
            $table->dropColumn(['orderPlacedDate', 'receivedDate', 'cancelledDate']);
        });
    }
};
