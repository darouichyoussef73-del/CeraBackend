<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        try {
            $bookings = Booking::all();
            return response()->json(['success' => true, 'bookings' => $bookings]);
        } catch (\Exception $e) {
            Log::error('Booking index error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            return response()->json(['success' => true, 'booking' => $booking]);
        } catch (\Exception $e) {
            Log::error('Booking show error: ' . $e->getMessage());
            return response()->json(['error' => 'Booking not found'], 404);
        }
    }
    public function store(Request $request)
    {
        try {
            $payload = $request->all();

            if ($request->has('data')) {
                $json = json_decode($request->input('data'), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                    $payload = array_merge($payload, $json);
                }
            }

            $validator = Validator::make($payload, [
                'serviceType' => 'required|string|max:100',
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:50',
                'date' => 'nullable|date',
                'time' => 'nullable|string|max:50',
                'participants' => 'nullable|string|max:50',
                'experience' => 'nullable|string|max:255',
                'pieceType' => 'nullable|string|max:255',
                'dimensions' => 'nullable|string|max:255',
                'glazePreference' => 'nullable|string|max:255',
                'message' => 'nullable|string',
                'newsletter' => 'nullable|boolean',
                'user_id' => 'nullable|integer|exists:users,id',
                'submittedAt' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('vision_image')) {
                $data['vision_image'] = $request->file('vision_image')->store('bookings', 'public');
            } elseif ($request->hasFile('visionImage')) {
                $data['vision_image'] = $request->file('visionImage')->store('bookings', 'public');
            }

            $booking = Booking::create([
                'user_id' => $data['user_id'] ?? null,
                'service_type' => $data['serviceType'],
                'full_name' => $data['fullName'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'preferred_date' => $data['date'] ?? null,
                'preferred_time' => $data['time'] ?? null,
                'participants' => $data['participants'] ?? null,
                'experience' => $data['experience'] ?? null,
                'piece_type' => $data['pieceType'] ?? null,
                'dimensions' => $data['dimensions'] ?? null,
                'glaze_preference' => $data['glazePreference'] ?? null,
                'message' => $data['message'] ?? null,
                'newsletter' => isset($data['newsletter']) ? filter_var($data['newsletter'], FILTER_VALIDATE_BOOLEAN) : false,
                'vision_image' => $data['vision_image'] ?? null,
                'submitted_at' => $data['submittedAt'] ?? now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully.',
                'booking' => $booking,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Booking store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }


    }
    //  public function update(Request $request, $id)
    // {
    //     try {
    //         $booking = Booking::findOrFail($id);
    //         $booking->update($request->all());
    //         return response()->json(['success' => true, 'booking' => $booking]);
    //     } catch (\Exception $e) {
    //         Log::error('Booking update error: ' . $e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
    public function update(Request $request, $id)
{
    try {
        $booking = Booking::findOrFail($id);

        $payload = $request->all();

        if ($request->has('data')) {
            $json = json_decode($request->input('data'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                $payload = array_merge($payload, $json);
            }
        }

        $validator = Validator::make($payload, [
            'serviceType' => 'sometimes|required|string|max:100',
            'fullName' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'date' => 'nullable|date',
            'time' => 'nullable|string|max:50',
            'participants' => 'nullable|string|max:50',
            'experience' => 'nullable|string|max:255',
            'pieceType' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'glazePreference' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'newsletter' => 'nullable|boolean',
            'user_id' => 'nullable|integer|exists:users,id',
            'submittedAt' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('vision_image')) {
            $data['vision_image'] = $request->file('vision_image')->store('bookings', 'public');
        } elseif ($request->hasFile('visionImage')) {
            $data['vision_image'] = $request->file('visionImage')->store('bookings', 'public');
        }

        $booking->update([
            'user_id' => $data['user_id'] ?? $booking->user_id,
            'service_type' => $data['serviceType'] ?? $booking->service_type,
            'full_name' => $data['fullName'] ?? $booking->full_name,
            'email' => $data['email'] ?? $booking->email,
            'phone' => $data['phone'] ?? $booking->phone,
            'preferred_date' => $data['date'] ?? $booking->preferred_date,
            'preferred_time' => $data['time'] ?? $booking->preferred_time,
            'participants' => $data['participants'] ?? $booking->participants,
            'experience' => $data['experience'] ?? $booking->experience,
            'piece_type' => $data['pieceType'] ?? $booking->piece_type,
            'dimensions' => $data['dimensions'] ?? $booking->dimensions,
            'glaze_preference' => $data['glazePreference'] ?? $booking->glaze_preference,
            'message' => $data['message'] ?? $booking->message,
            'newsletter' => isset($data['newsletter']) ? filter_var($data['newsletter'], FILTER_VALIDATE_BOOLEAN) : $booking->newsletter,
            'vision_image' => $data['vision_image'] ?? $booking->vision_image,
            'submitted_at' => $data['submittedAt'] ?? $booking->submitted_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully.',
            'booking' => $booking->fresh(),
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Booking not found'], 404);
    } catch (\Exception $e) {
        Log::error('Booking update error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function destroy($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();
            return response()->json(['success' => true, 'message' => 'Booking deleted']);
        } catch (\Exception $e) {
            Log::error('Booking destroy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
