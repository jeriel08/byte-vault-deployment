<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Don’t modify orderID—it’s already correct as bigint PK
            if (!Schema::hasColumn('orders', 'customerID')) {
                $table->unsignedBigInteger('customerID')->after('orderID');
                $table->foreign('customerID')->references('customerID')->on('customers')->onDelete('cascade');
            }
            if (!Schema::hasColumn('orders', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('customerID');
                $table->foreign('product_id')->references('productID')->on('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('orders', 'quantity')) {
                $table->integer('quantity')->after('product_id');
            }
            if (!Schema::hasColumn('orders', 'amount')) {
                $table->decimal('amount', 8, 2)->after('quantity');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->after('amount');
            }
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->after('payment_status');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customerID']);
            $table->dropForeign(['product_id']);
            $table->dropColumn(['customerID', 'product_id', 'quantity', 'amount', 'payment_status', 'status']);
        });
    }
}