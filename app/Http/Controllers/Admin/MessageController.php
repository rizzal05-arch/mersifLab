<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display all messages
     */
    public function index()
    {
        $messages = Message::orderBy('created_at', 'desc')->paginate(25);
        $unreadCount = Message::where('is_read', false)->count();
        
        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display a specific message
     */
    public function show(Message $message)
    {
        // Mark as read
        if (!$message->is_read) {
            $message->markAsRead();
        }

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Delete a message
     */
    public function destroy(Message $message)
    {
        $message->delete();
        
        return redirect()->route('admin.messages.index')
                         ->with('success', 'Message deleted successfully');
    }

    /**
     * Mark message as read
     */
    public function markRead(Message $message)
    {
        $message->markAsRead();
        
        return back()->with('success', 'Message marked as read');
    }

    /**
     * Get unread messages count (untuk AJAX)
     */
    public function unreadCount()
    {
        $count = Message::where('is_read', false)->count();
        
        return response()->json(['unread_count' => $count]);
    }
}
