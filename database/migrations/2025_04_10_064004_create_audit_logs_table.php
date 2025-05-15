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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('logID');
            $table->string('tableName');
            $table->unsignedBigInteger('recordID');
            $table->string('actionType'); // e.g., CREATE, UPDATE, DELETE, LOGIN, LOGOUT
            $table->string('columnName')->nullable();
            $table->text('oldValue')->nullable();
            $table->text('newValue')->nullable();
            $table->unsignedBigInteger('employeeID')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->foreign('employeeID')->references('employeeID')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
