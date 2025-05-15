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
        Schema::create('return_to_suppliers', function (Blueprint $table) {
            $table->id('returnSupplierID');
            $table->unsignedBigInteger('supplierID');
            $table->date('returnDate');
            $table->string('returnSupplierReason');
            $table->enum('status', ['Pending', 'Completed', 'Rejected'])->default('Pending');
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('supplierID')->references('supplierID')->on('suppliers');
            $table->foreign('created_by')->references('employeeID')->on('employees');
            $table->foreign('updated_by')->references('employeeID')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_to_suppliers');
    }
};
