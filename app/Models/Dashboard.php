<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    // No table needed since this is an aggregator model
    protected $table = null;

    /**
     * Get the total sales (sum of total from orders).
     *
     * @return float
     */
    public static function getTotalSales()
    {
        return DB::table('orders')
            ->sum('total');
    }

    /**
     * Get the total number of orders (count of orders).
     *
     * @return int
     */
    public static function getTotalOrders()
    {
        return DB::table('orders')
            ->count();
    }

    /**
     * Get the total products in stock (sum of stockQuantity from products).
     *
     * @return int
     */
    public static function getTotalProductsInStock()
    {
        return DB::table('products')
            ->sum('stockQuantity');
    }

    /**
     * Get the count of low stock products (stockQuantity <= 10).
     *
     * @return int
     */
    public static function getLowStockProducts()
    {
        return DB::table('products')
            ->where('stockQuantity', '<=', 10)
            ->where('stockQuantity', '>', 0)
            ->count();
    }

    /**
     * Get total sales per day from orders.
     *
     * @return array
     */
    public static function getDailySales()
    {
        return DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total_sales'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get the distribution of products per category.
     *
     * @return array
     */
    public static function getCategoryDistribution()
    {
        return DB::table('products')
            ->join('categories', 'products.categoryID', '=', 'categories.categoryID')
            ->select('categories.categoryName', DB::raw('COUNT(products.productID) as product_count'))
            ->groupBy('categories.categoryName')
            ->get()
            ->toArray();
    }

    /**
     * Get total sales (revenue) per category from orderline.
     *
     * @return array
     */
    public static function getSalesByCategory()
    {
        return DB::table('orderline')
            ->join('products', 'orderline.productID', '=', 'products.productID')
            ->join('categories', 'products.categoryID', '=', 'categories.categoryID')
            ->select(
                'categories.categoryName',
                DB::raw('SUM(orderline.price * orderline.quantity) as total_sales')
            )
            ->groupBy('categories.categoryName')
            ->get()
            ->toArray();
    }

    /**
     * Get top-selling products (based on total quantity sold in orderline).
     *
     * @return array
     */
    public static function getTopSellingProducts()
    {
        return DB::table('orderline')
            ->join('products', 'orderline.productID', '=', 'products.productID')
            ->select(
                'products.productName',
                DB::raw('SUM(orderline.quantity) as total_quantity'),
                DB::raw('SUM(orderline.price * orderline.quantity) as total_revenue')
            )
            ->groupBy('products.productName')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->toArray();
    }
}