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
        Schema::table('return_to_suppliers', function (Blueprint $table) {
            //
            $table->dropColumn('status'); // Remove old status
            $table->dateTime('adjustmentDatePlaced')->nullable()->after('returnSupplierReason');
            $table->dateTime('completionDate')->nullable()->after('adjustmentDatePlaced');
            $table->dateTime('cancellationDate')->nullable()->after('completionDate');
            $table->text('cancellationRemark')->nullable()->after('cancellationDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_to_suppliers', function (Blueprint $table) {
            $table->dropColumn('adjustmentDatePlaced');
            $table->dropColumn('completionDate');
            $table->dropColumn('cancellationDate');
            $table->dropColumn('cancellationRemark');
            $table->string('status')->after('returnSupplierReason'); // Re-add old status
        });
    }
};
