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
        Schema::table('return_to_suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('supplierOrderID')->nullable()->after('supplierID');
            $table->foreign('supplierOrderID')->references('supplierOrderID')->on('supplier_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_to_suppliers', function (Blueprint $table) {
            $table->dropForeign(['supplierOrderID']);
            $table->dropColumn('supplierOrderID');
        });
    }
};
