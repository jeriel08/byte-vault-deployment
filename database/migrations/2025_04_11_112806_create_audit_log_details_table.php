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
        Schema::create('audit_log_details', function (Blueprint $table) {
            $table->id('detailID');
            $table->unsignedBigInteger('logID');
            $table->string('columnName');
            $table->text('oldValue')->nullable();
            $table->text('newValue')->nullable();
            $table->foreign('logID')->references('logID')->on('audit_logs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log_details');
    }
};
