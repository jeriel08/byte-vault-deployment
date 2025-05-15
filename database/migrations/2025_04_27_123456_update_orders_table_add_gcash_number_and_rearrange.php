<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Back up existing data
        $orders = DB::table('orders')->get();
        $orderlines = DB::table('orderline')->get();

        // Step 2: Drop foreign key constraints
        Schema::table('orderline', function (Blueprint $table) {
            $table->dropForeign(['orderID']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customerID']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        // Step 3: Drop the orders table
        Schema::dropIfExists('orders');

        // Step 4: Recreate the orders table with new column order
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('orderID')->autoIncrement();
            $table->unsignedBigInteger('customerID');
            $table->unsignedInteger('total_items');
            $table->string('payment_status');
            $table->string('gcash_number', 11)->nullable();
            $table->decimal('amount_received', 10, 2)->nullable();
            $table->decimal('change', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('customerID')->references('customerID')->on('customers')->onDelete('cascade');
            $table->foreign('created_by')->references('employeeID')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employeeID')->on('employees')->onDelete('set null');
        });

        // Step 5: Restore orders data
        foreach ($orders as $order) {
            DB::table('orders')->insert([
                'orderID' => $order->orderID,
                'customerID' => $order->customerID,
                'total_items' => $order->total_items,
                'payment_status' => $order->payment_status,
                'gcash_number' => null, // New column, no data yet
                'amount_received' => $order->amount_received,
                'change' => $order->change,
                'total' => $order->total,
                'created_by' => $order->created_by,
                'updated_by' => $order->updated_by,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
        }

        // Step 6: Reapply foreign key constraint on orderline
        Schema::table('orderline', function (Blueprint $table) {
            $table->foreign('orderID')->references('orderID')->on('orders')->onDelete('cascade');
        });

        // Step 7: Restore orderline data (in case it was affected)
        Schema::table('orderline', function (Blueprint $table) {
            $table->dropIfExists('orderline');
        });
        Schema::create('orderline', function (Blueprint $table) {
            $table->unsignedBigInteger('orderLineID')->autoIncrement();
            $table->unsignedBigInteger('productID');
            $table->unsignedBigInteger('orderID');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
            $table->foreign('orderID')->references('orderID')->on('orders')->onDelete('cascade');
        });

        foreach ($orderlines as $orderline) {
            DB::table('orderline')->insert([
                'orderLineID' => $orderline->orderLineID,
                'productID' => $orderline->productID,
                'orderID' => $orderline->orderID,
                'quantity' => $orderline->quantity,
                'price' => $orderline->price,
                'created_at' => $orderline->created_at,
                'updated_at' => $orderline->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        // Step 1: Back up existing data
        $orders = DB::table('orders')->get();
        $orderlines = DB::table('orderline')->get();

        // Step 2: Drop foreign key constraints
        Schema::table('orderline', function (Blueprint $table) {
            $table->dropForeign(['orderID']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customerID']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        // Step 3: Drop the orders table
        Schema::dropIfExists('orders');

        // Step 4: Recreate the orders table with original structure
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('orderID')->autoIncrement();
            $table->unsignedBigInteger('customerID');
            $table->unsignedInteger('total_items');
            $table->string('payment_status');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->decimal('amount_received', 10, 2)->nullable();
            $table->decimal('change', 10, 2)->nullable();
            $table->decimal('total', 10, 2);

            $table->foreign('customerID')->references('customerID')->on('customers')->onDelete('cascade');
            $table->foreign('created_by')->references('employeeID')->on('employees')->onDelete('set null');
            $table->foreign('updated_by')->references('employeeID')->on('employees')->onDelete('set null');
        });

        // Step 5: Restore orders data (without gcash_number)
        foreach ($orders as $order) {
            DB::table('orders')->insert([
                'orderID' => $order->orderID,
                'customerID' => $order->customerID,
                'total_items' => $order->total_items,
                'payment_status' => $order->payment_status,
                'created_by' => $order->created_by,
                'updated_by' => $order->updated_by,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'amount_received' => $order->amount_received,
                'change' => $order->change,
                'total' => $order->total,
            ]);
        }

        // Step 6: Reapply foreign key constraint on orderline
        Schema::table('orderline', function (Blueprint $table) {
            $table->foreign('orderID')->references('orderID')->on('orders')->onDelete('cascade');
        });

        // Step 7: Restore orderline data
        Schema::table('orderline', function (Blueprint $table) {
            $table->dropIfExists('orderline');
        });
        Schema::create('orderline', function (Blueprint $table) {
            $table->unsignedBigInteger('orderLineID')->autoIncrement();
            $table->unsignedBigInteger('productID');
            $table->unsignedBigInteger('orderID');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->foreign('productID')->references('productID')->on('products')->onDelete('cascade');
            $table->foreign('orderID')->references('orderID')->on('orders')->onDelete('cascade');
        });

        foreach ($orderlines as $orderline) {
            DB::table('orderline')->insert([
                'orderLineID' => $orderline->orderLineID,
                'productID' => $orderline->productID,
                'orderID' => $orderline->orderID,
                'quantity' => $orderline->quantity,
                'price' => $orderline->price,
                'created_at' => $orderline->created_at,
                'updated_at' => $orderline->updated_at,
            ]);
        }
    }
};