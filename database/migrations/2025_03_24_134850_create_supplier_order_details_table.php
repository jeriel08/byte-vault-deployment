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
        Schema::create('supplier_order_details', function (Blueprint $table) {
            $table->id('supplierOrderDetailID');
            $table->unsignedBigInteger('supplierOrderID');
            $table->unsignedBigInteger('productID');
            $table->integer('quantity');
            $table->decimal('unitCost', 10, 2);
            $table->integer('receivedQuantity')->default(0);
            $table->enum('status', ['Pending', 'Cancelled', 'Received'])->default('Pending');
            $table->timestamps();

            $table->foreign('supplierOrderID')->references('supplierOrderID')->on('supplier_orders')->onDelete('cascade');
            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_order_details');
    }
};
