<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Database\Seeder;

class ProductsAndOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create products
        $products = [
            [
                'name' => 'CPU',
                'description' => 'High-performance CPU',
                'price' => 15000,
                'image' => 'cpu.png',
            ],
            [
                'name' => 'Motherboard',
                'description' => 'Gaming motherboard',
                'price' => 12000,
                'image' => 'motherboard.png',
            ],
            [
                'name' => 'RAM',
                'description' => '16GB DDR4 RAM',
                'price' => 5000,
                'image' => 'ram.png',
            ],
            [
                'name' => 'Hard Drive',
                'description' => '1TB SSD',
                'price' => 8000,
                'image' => 'harddrive.png',
            ],
            [
                'name' => 'Monitor',
                'description' => '27-inch 4K Monitor',
                'price' => 20000,
                'image' => 'monitor.png',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create orders
        $customers = ['Jorge B.', 'Maria L.', 'John D.', 'Sarah K.'];
        $statuses = ['Pending', 'Delivered', 'Processing', 'Shipped'];
        $paymentStatuses = ['Pending', 'Paid', 'Failed', 'Refunded'];

        for ($i = 1; $i <= 20; $i++) {
            $productId = rand(1, 5);
            $product = Product::find($productId);
            
            Order::create([
                'order_id' => '#' . rand(10000, 99999),
                'customer_name' => $customers[array_rand($customers)],
                'product_id' => $productId,
                'quantity' => rand(1, 5),
                'amount' => $product->price,
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}