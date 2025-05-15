<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get all products for counts
        $allProducts = cache()->remember('all_products_counts', 3600, function () {
            return Product::with('brand', 'category')->get();
        });

        // Start with query for filtered products
        $query = Product::with('brand', 'category');

        // Search by product name
        if ($request->has('search') && !empty($request->input('search'))) {
            $query->whereRaw('LOWER(productName) LIKE ?', ['%' . strtolower($request->input('search')) . '%']);
        }

        // Filter by product status
        if ($request->has('productStatus')) {
            $status = $request->input('productStatus');
            if ($status !== 'All') {
                $query->where('productStatus', $status);
            }
        }

        // Filter by categories
        if ($request->has('category') && !empty($request->input('category'))) {
            $query->whereIn('categoryID', $request->input('category'));
        }

        // Filter by brands
        if ($request->has('brand') && !empty($request->input('brand'))) {
            $query->whereIn('brandID', $request->input('brand'));
        }

        // Handle sorting
        if ($request->has('sortBy')) {
            switch ($request->input('sortBy')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('productName', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('productName', 'desc');
                    break;
            }
        }

        // Get paginated filtered products
        $products = $query->paginate(5)->appends($request->query());

        return view('admin.products.index', compact('products', 'allProducts'));
    }

    public function create()
    {
        $brands = Brand::where('brandStatus', 'Active')->get();
        $categories = Category::where('categoryStatus', 'Active')->get();
        return view('admin.products.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'productName' => 'required|string|max:255',
            'productDescription' => 'nullable|string',
            'brandID' => 'required|exists:brands,brandID',
            'categoryID' => 'required|exists:categories,categoryID',
            'productStatus' => 'required|in:Active,Inactive',
        ]);

        Product::create([
            'productName' => $request->productName,
            'productDescription' => $request->productDescription,
            'brandID' => $request->brandID,
            'categoryID' => $request->categoryID,
            'price' => 0,  // Default value
            'stockQuantity' => 0,  // Default value
            'productStatus' => $request->productStatus,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        $brands = Brand::where('brandStatus', 'Active')->get();
        $categories = Category::where('categoryStatus', 'Active')->get();
        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    public function update(Request $request, $productID)
    {
        $request->validate([
            'productName' => 'required|string|max:255',
            'productDescription' => 'nullable|string',
            'brandID' => 'required|exists:brands,brandID',
            'categoryID' => 'required|exists:categories,categoryID',
            'productStatus' => 'required|in:Active,Inactive',
        ]);

        $product = Product::findOrFail($productID);
        $product->update([
            'productName' => $request->productName,
            'productDescription' => $request->productDescription,
            'brandID' => $request->brandID,
            'categoryID' => $request->categoryID,
            'productStatus' => $request->productStatus,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        //
    }
}
