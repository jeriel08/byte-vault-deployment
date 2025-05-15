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
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns
            $table->decimal('amount_received', 10, 2)->nullable()->default(null);
            $table->decimal('change', 10, 2)->nullable()->default(null);
            $table->decimal('total', 10, 2);

            // Rename product_id to productID
            $table->renameColumn('product_id', 'productID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('orders', function (Blueprint $table) {
            // Reverse the changes
            $table->dropColumn('amount_received');
            $table->dropColumn('change');
            $table->dropColumn('total');

            // Rename productID back to product_id
            $table->renameColumn('productID', 'product_id');
        });
    }
};
