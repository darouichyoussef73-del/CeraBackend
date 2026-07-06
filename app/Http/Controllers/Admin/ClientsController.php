<?php

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\User;
// use App\Models\Order;
// use App\Models\Payment;
// use Illuminate\Support\Facades\Schema;

// class ClientsController extends Controller
// {
//     public function index()
//     {
        
//         $hasOrders = Schema::hasTable('orders');
//         $hasPayments = Schema::hasTable('payments');

//         $clients = User::orderBy('name')->get()->map(function ($u) use ($hasOrders, $hasPayments) {
//             $orders = $hasOrders ? Order::where('client_id', $u->id)->count() : 0;
//             $spent = $hasPayments ? Payment::where('client_id', $u->id)->sum('amount') : 0;
//             return [
//                 'id' => $u->id,
//                 'name' => $u->name,
//                 'email' => $u->email,
//                 'phone' => $u->phone ?? '',
//                 'avatar' => $u->avatar ?? strtoupper(substr($u->name, 0, 1)),
//                 'orders' => $orders,
//                 'spent' => (float) $spent,
//                 'status' => $u->status ?? 'Active',
//             ];
//         });

//         return response()->json($clients)->header('Access-Control-Allow-Origin', '*');
//     }

//     public function show($id)
//     {
//         $user = User::findOrFail($id);
//         $hasOrders = Schema::hasTable('orders');
//         $hasPayments = Schema::hasTable('payments');

//         $orders = $hasOrders ? Order::where('client_id', $user->id)->count() : 0;
//         $spent = $hasPayments ? Payment::where('client_id', $user->id)->sum('amount') : 0;

//         $payload = [
//             'id' => $user->id,
//             'name' => $user->name,
//             'email' => $user->email,
//             'phone' => $user->phone ?? '',
//             'avatar' => $user->avatar ?? strtoupper(substr($user->name, 0, 1)),
//             'orders' => $orders,
//             'spent' => (float) $spent,
//             'status' => $user->status ?? 'Active',
//         ];
//         return response()->json($payload)->header('Access-Control-Allow-Origin', '*');
//     }

//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'name' => 'required|string',
//             'email' => 'required|email|unique:users,email',
//             'avatar' => 'nullable|string',
//             'status' => 'nullable|string'
//         ]);

//         // allow optional password; User model casts 'password' to hashed
//         $data['password'] = $data['password'] ?? null;
//         $user = User::create($data);
//         return response()->json($user, 201);
//     }

//     public function update(Request $request, $id)
//     {
//         $user = User::findOrFail($id);
//         $data = $request->validate([
//             'name' => 'sometimes|string',
//             'email' => 'sometimes|email',
//             'avatar' => 'sometimes|string',
//             'status' => 'sometimes|string'
//         ]);
//         $user->update($data);
//         return response()->json($user);
//     }

//     public function destroy($id)
//     {
//         $user = User::findOrFail($id);
//         $user->delete();
//         return response()->json(['message' => 'Client deleted']);
//     }
// }


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;

class ClientsController extends Controller
{
    public function index()
    {
        $hasOrders = Schema::hasTable('orders');

        $clients = User::orderBy('name')->get()->map(function ($u) use ($hasOrders) {
            // Use user_id instead of client_id (matches your orders schema)
            $orders = $hasOrders ? Order::where('user_id', $u->id)->count() : 0;
            $spent = $hasOrders ? Order::where('user_id', $u->id)->sum('total') : 0;
            
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone ?? '',
                'avatar' => $u->avatar ?? strtoupper(substr($u->name, 0, 1)),
                'orders' => $orders,
                'spent' => (float) $spent,
                'status' => $u->status ?? 'Active',
            ];
        });

        return response()->json($clients)->header('Access-Control-Allow-Origin', '*');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $hasOrders = Schema::hasTable('orders');

        $orders = $hasOrders ? Order::where('user_id', $user->id)->count() : 0;
        $spent = $hasOrders ? Order::where('user_id', $user->id)->sum('total') : 0;

        $payload = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'avatar' => $user->avatar ?? strtoupper(substr($user->name, 0, 1)),
            'orders' => $orders,
            'spent' => (float) $spent,
            'status' => $user->status ?? 'Active',
        ];
        return response()->json($payload)->header('Access-Control-Allow-Origin', '*');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'avatar' => 'nullable|string',
            'status' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $data['password'] = bcrypt($data['password'] ?? 'password123');
        $user = User::create($data);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string',
            'avatar' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);
        $user->update($data);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Client deleted']);
    }
}