<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuditLogController extends Controller
{
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
        $query = AuditLog::with(['employee', 'details'])->orderBy('logID', 'desc');

        // Filter by Employee ID
        if ($request->has('employeeID') && !empty($request->employeeID)) {
            $query->where('employeeID', $request->employeeID);
        }

        // Filter by Action Type
        if ($request->has('action_type') && !empty($request->action_type)) {
            $query->where('actionType', $request->action_type);
        }

        // Filter by Table Name
        if ($request->has('table_name') && !empty($request->table_name)) {
            $query->where('tableName', $request->table_name);
        }

        // Filter by Date
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('timestamp', $request->date);
        }

        $auditLogs = $query->paginate(10)->appends($request->query());

        $tableNames = [
            'employees' => 'Employee',
            'products' => 'Product',
            'orders' => 'Order',
            'adjustments' => 'Adjustment',
            'stock_out_details' => 'Stock Out Detail',
            'stock_outs' => 'Stock Out',
            'return_to_suppliers' => 'Return to Supplier',
            'suppliers' => 'Supplier',
            'categories' => 'Category',
            'brands' => 'Brand',
            'supplier_orders' => 'Supplier Order',
            'supplier_order_details' => 'Supplier Order Detail',
        ];

        // Get all users for the filter
        $users = User::orderBy('firstName')->orderBy('lastName')
            ->get(['employeeID', 'firstName', 'lastName']);

        return view('admin.audit.index', compact('auditLogs', 'tableNames', 'users'));
    }
}
