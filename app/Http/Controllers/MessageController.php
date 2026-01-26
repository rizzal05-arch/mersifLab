<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|min:10|max:5000',
            ]);

            $message = Message::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully! Our team will contact you soon.',
                'data' => $message
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the message. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
