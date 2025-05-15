<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('productID');
            $table->string('productName');
            $table->text('productDescription')->nullable();
            $table->unsignedBigInteger('brandID');
            $table->unsignedBigInteger('categoryID');
            $table->decimal('price', 10, 2); // e.g., 99999999.99
            $table->unsignedInteger('stockQuantity')->default(0);
            $table->string('productStatus')->default('Active'); // 'Active' or 'Inactive'
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('brandID')->references('brandID')->on('brands')->onDelete('cascade');
            $table->foreign('categoryID')->references('categoryID')->on('categories')->onDelete('cascade');
            $table->foreign('created_by')->references('employeeID')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employeeID')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
