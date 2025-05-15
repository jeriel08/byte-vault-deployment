<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Order::with(['creator', 'updater'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('logs.index', compact('logs'));
    }
}