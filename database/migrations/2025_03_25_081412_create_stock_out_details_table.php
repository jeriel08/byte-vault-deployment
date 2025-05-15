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
        Schema::create('stock_out_details', function (Blueprint $table) {
            $table->id('stockOutDetailID');
            $table->unsignedBigInteger('stockOutID');
            $table->unsignedBigInteger('productID');
            $table->integer('quantity');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('stockOutID')->references('stockOutID')->on('stock_outs')->onDelete('cascade');
            $table->foreign('productID')->references('productID')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_details');
    }
};
