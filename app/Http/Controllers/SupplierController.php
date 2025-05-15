<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Handle search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('supplierName', 'like', "%{$search}%")
                ->orWhere('supplierAddress', 'like', "%{$search}%")
                ->orWhere('supplierPhoneNumber', 'like', "%{$search}%");
        }

        // Paginate results (15 per page)
        $suppliers = $query->paginate(10)->appends($request->query());

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    // Store a new supplier
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'supplierName' => ['required', 'string', 'max:255'],
            'supplierAddress' => ['nullable', 'string'],
            'supplierPhoneNumber' => ['nullable', 'string', 'max:20', 'regex:/^(0\d{10}|\+63\d{10})$/'],
            'supplierProfileImage' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'supplierStatus' => ['required', 'in:Active,Inactive'],
        ]);

        // Handle image upload (if provided)
        $imagePath = null;
        if ($request->hasFile('supplierProfileImage')) {
            $imagePath = $request->file('supplierProfileImage')->store('supplier_images', 'public');
        }

        // Create the supplier
        Supplier::create([
            'supplierName' => $request->supplierName,
            'supplierAddress' => $request->supplierAddress,
            'supplierPhoneNumber' => $request->supplierPhoneNumber,
            'supplierProfileImage' => $imagePath,
            'supplierStatus' => $request->supplierStatus,
            'created_by' => auth()->id(), // Assign only the user ID
        ]);

        // Redirect with a success message
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $supplierID)
    {
        // Validate the request data
        $request->validate([
            'supplierAddress' => 'nullable|string',
            'supplierPhoneNumber' => 'nullable|string|max:20',
            'supplierProfileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'supplierStatus' => 'nullable|string', // Add validation for supplierStatus
        ]);

        // Find the supplier by ID
        $supplier = Supplier::findOrFail($supplierID);

        // Handle image upload (if provided)
        if ($request->hasFile('supplierProfileImage')) {
            // Delete the old image if it exists
            if ($supplier->supplierProfileImage) {
                Storage::delete('public/' . $supplier->supplierProfileImage);
            }
            // Store the new image
            $imagePath = $request->file('supplierProfileImage')->store('supplier_images', 'public');
            $supplier->supplierProfileImage = $imagePath;
        }

        // Update the supplier data
        $supplier->update([
            'supplierAddress' => $request->supplierAddress,
            'supplierPhoneNumber' => $request->supplierPhoneNumber,
            'supplierStatus' => $request->supplierStatus, // Add supplierStatus
        ]);

        // Redirect with a success message
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
