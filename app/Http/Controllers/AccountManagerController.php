<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountManagerController extends BaseController
{
    //
    public function __construct()
    {
        // Restrict to admins only
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'Admin') {
                abort(403, 'Only admins can access this page.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {

        $query = User::query();


        $query->where('employeeID', '!=', Auth::user()->employeeID);


        $selectedRoles = $request->input('roles', []);
        $selectedStatus = $request->input('status');

        if (!empty($selectedRoles)) {

            if (!is_array($selectedRoles)) {
                $selectedRoles = [$selectedRoles];
            }

            $query->whereIn('role', $selectedRoles);
        }

        if (!empty($selectedStatus)) {
            $query->where('status', $selectedStatus);
        }

        $employees = $query->get();

        return view('admin.accounts.account-manager', compact('employees'));
    }

    public function add()
    {
        return view('admin.accounts.add-account');
    }

    public function update(Request $request, $employeeID)
    {
        $employee = User::findOrFail($employeeID);

        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->employeeID . ',employeeID',
            'phoneNumber' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:Employee,Manager,Admin',
            'status' => 'required|in:Active,Inactive',
        ]);

        $employee->firstName = $request->firstName;
        $employee->lastName = $request->lastName;
        $employee->email = $request->email;
        $employee->phoneNumber = $request->phoneNumber;
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        $employee->role = $request->role;
        $employee->status = $request->status;
        $employee->updated_at = now();
        $employee->updated_by = auth()->user()->employeeID;
        $employee->save();

        return redirect()->route('account.manager')->with('success', 'Account updated successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'phoneNumber' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Employee,Manager,Admin',
            'status' => 'required|in:Active,Inactive',
        ]);

        $employee = new User();
        $employee->firstName = $request->firstName;
        $employee->lastName = $request->lastName;
        $employee->email = $request->email;
        $employee->phoneNumber = $request->phoneNumber;
        $employee->password = Hash::make($request->password);
        $employee->role = $request->role;
        $employee->status = $request->status;
        $employee->created_at = now();
        $employee->created_by = auth()->user()->employeeID;
        $employee->updated_at = now();
        $employee->updated_by = auth()->user()->employeeID;
        $employee->save();

        return redirect()->route('account.manager')->with('success', 'Account added successfully');
    }

    public function edit($employeeID)
    {
        $employee = User::findOrFail($employeeID);
        return view('admin.accounts.edit-account', compact('employee'));
    }
}
