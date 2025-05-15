<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $brands = Brand::query()
            ->when($search, function ($query, $search) {
                return $query->where('brandName', 'like', '%' . $search . '%');
            })
            ->where('brandStatus', 'Active') // Align with POS logic
            ->orderBy('brandName')
            ->paginate(10); // 10 brands per page

        return view('admin.products.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.products.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'brandName' => ['required', 'string', 'max:255'],
            'brandStatus' => ['required', 'in:Active,Inactive'],
            'brandProfileImage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'brandName' => $request->brandName,
            'brandStatus' => $request->brandStatus,
            'created_by' => auth()->id(), // Assign only the user ID
        ];

        if ($request->hasFile('brandProfileImage')) {
            $data['brandProfileImage'] = $request->file('brandProfileImage')->store('brand_images', 'public');
        }

        Brand::create($data);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($brandID)
    {
        $brand = Brand::findOrFail($brandID);
        return view('admin.products.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $brandID)
    {
        $request->validate([
            'brandName' => ['required', 'string', 'max:255'],
            'brandStatus' => ['required', 'in:Active,Inactive'],
            'brandProfileImage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $brand = Brand::findOrFail($brandID);

        $data = [
            'brandName' => $request->brandName,
            'brandStatus' => $request->brandStatus,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('brandProfileImage')) {
            if ($brand->brandProfileImage) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($brand->brandProfileImage);
            }
            $data['brandProfileImage'] = $request->file('brandProfileImage')->store('brand_images', 'public');
        }

        $brand->update($data);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
