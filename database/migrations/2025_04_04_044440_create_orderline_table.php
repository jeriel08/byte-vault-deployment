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
        Schema::create('orderline', function (Blueprint $table) {
            $table->id('orderLineID'); // Auto-incrementing primary key
            $table->unsignedBigInteger('productID'); // Foreign key to products
            $table->unsignedBigInteger('orderID'); // Foreign key to orders
            $table->integer('quantity')->unsigned(); // Quantity of the product
            $table->decimal('price', 8, 2); // Price per unit (e.g., 999999.99)

            // Foreign key constraints
            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
            $table->foreign('orderID')->references('orderID')->on('orders')->onDelete('cascade');

            $table->timestamps(); // Optional: created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderline');
    }
};
