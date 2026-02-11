<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscribed users for admin monitoring.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');

        $subscribers = User::query()
            ->where('is_subscriber', true)
            ->when($q, function ($query, $q) {
                $like = '%' . $q . '%';
                $query->where(function ($q2) use ($like) {
                    $q2->where('name', 'like', $like)
                       ->orWhere('email', 'like', $like);
                });
            })
            ->orderBy('subscription_expires_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscriptions.index', compact('subscribers', 'q'));
    }
}
