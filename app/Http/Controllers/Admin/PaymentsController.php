<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentsController extends Controller
{
    public function index()
    {
        return response()->json(Payment::with('client')->orderBy('date','desc')->get());
    }

    public function show($id)
    {
        $payment = Payment::with('client')->findOrFail($id);
        return response()->json($payment);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'nullable|integer',
            'amount' => 'required|numeric',
            'method' => 'nullable|string',
            'status' => 'nullable|string',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create($data);
        return response()->json($payment, 201);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $data = $request->validate([
            'amount' => 'sometimes|numeric',
            'method' => 'sometimes|string',
            'status' => 'sometimes|string',
            'date' => 'sometimes|date',
            'notes' => 'sometimes|string',
        ]);
        $payment->update($data);
        return response()->json($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(['message' => 'Payment deleted']);
    }
}
