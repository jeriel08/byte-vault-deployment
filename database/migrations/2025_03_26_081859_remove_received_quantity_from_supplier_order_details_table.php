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
        Schema::table('supplier_order_details', function (Blueprint $table) {
            //
            $table->dropColumn('receivedQuantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_order_details', function (Blueprint $table) {
            //
            $table->integer('receivedQuantity')->default(0)->after('unitCost');
        });
    }
};
