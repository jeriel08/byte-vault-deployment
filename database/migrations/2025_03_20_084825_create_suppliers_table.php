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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supplierID'); // Primary key
            $table->string('supplierName');
            $table->text('supplierAddress')->nullable(); // Optional field
            $table->string('supplierPhoneNumber')->nullable(); // Optional field
            $table->string('supplierProfileImage')->nullable(); // Optional field for image path
            $table->string('supplierStatus')->default('Active'); // Default value
            $table->timestamps(); // Adds `created_at` and `updated_at` columns
            $table->unsignedBigInteger('created_by')->nullable(); // User who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // User who updated the record

            // Foreign key constraints (optional)
            $table->foreign('created_by')->references('employeeID')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employeeID')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
