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
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id('supplierOrderID');
            $table->unsignedBigInteger('supplierID');
            $table->date('orderDate');
            $table->date('expectedDeliveryDate')->nullable();
            $table->enum('status', ['Pending', 'Cancelled', 'Received'])->default('Pending');
            $table->decimal('totalCost', 10, 2)->default(0.00);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('supplierID')->references('supplierID')->on('suppliers')->onDelete('cascade');
            $table->foreign('created_by')->references('employeeID')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employeeID')->on('employees')->onDelete('set null');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_orders');
    }
};
