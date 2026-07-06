<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class StatsController extends Controller
{
    public function overview()
    {
        $totalClients = User::count();
        $totalOrders = Order::count();
        $revenue = Order::sum('amount');
        $pendingPayments = Order::where('status','Pending')->count();

        return response()->json([
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'revenue' => $revenue,
            'pendingPayments' => $pendingPayments
        ]);
    }
}
