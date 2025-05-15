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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('categoryID');
            $table->string('categoryName');
            $table->text('categoryDescription')->nullable();
            $table->unsignedBigInteger('parentCategoryID')->nullable(); // Self-referencing FK
            $table->string('categoryStatus')->default('Active'); // 'Active' or 'Inactive'
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('parentCategoryID')->references('categoryID')->on('categories')->onDelete('set null');
            $table->foreign('created_by')->references('employeeID')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employeeID')->on('employees')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
