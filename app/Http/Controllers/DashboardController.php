<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        $totalMateri = Materi::count();
        
        $data = [
            'courses' => $courses,
            'totalKursus' => $courses->count(),
            'totalMateri' => $totalMateri,
        ];

        if (auth()->user()->isAdmin()) {
            $data['users'] = User::where('role', 'user')->get();
            $data['totalUsers'] = User::where('role', 'user')->count();
            $data['activeSubscribers'] = User::where('is_subscriber', true)->count();
        }

        return view('dashboard', $data);
    }
}
