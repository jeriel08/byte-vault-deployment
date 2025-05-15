<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixOrdersTableCustomerColumn extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop customer_name if it exists
            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            // Add customerID if not already present
            if (!Schema::hasColumn('orders', 'customerID')) {
                $table->unsignedBigInteger('customerID')->after('order_id');
                $table->foreign('customerID')->references('customerID')->on('customers')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customerID']);
            $table->dropColumn('customerID');
            $table->string('customer_name')->after('order_id');
        });
    }
}