<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('customerID'); // Auto-incrementing primary key, matches your validation
            $table->string('name');              // Customer name, used in index and create views
            $table->timestamps();                // created_at and updated_at for tracking
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};