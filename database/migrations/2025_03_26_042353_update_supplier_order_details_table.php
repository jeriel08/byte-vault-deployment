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
        Schema::table('supplier_order_details', function (Blueprint $table) {
            // Drop the status column
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('supplier_order_details', function (Blueprint $table) {
            // Revert changes: add status back
            $table->enum('status', ['Received', 'Pending'])->default('Pending');
        });
    }
};
