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
        Schema::table('orders', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign('orders_product_id_foreign'); // Corrected name
            $table->dropColumn('productID');
            $table->dropColumn('quantity');
            $table->dropColumn('amount');
            $table->dropColumn('status');

            // Add the new total_items column
            $table->integer('total_items')->unsigned()->after('customerID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Re-add the dropped columns in case of rollback
            $table->bigInteger('productID')->unsigned()->after('customerID');
            $table->foreign('productID', 'orders_product_id_foreign') // Specify the exact name
                  ->references('productID')->on('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('amount', 8, 2);
            $table->string('status');

            // Drop the total_items column
            $table->dropColumn('total_items');
        });
    }
};