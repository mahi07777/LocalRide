<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\User;
use App\Models\Package;

class DashboardController extends Controller
{
    public function index(): View
    {
        // You can fetch data for your dashboard here (e.g., from the database)
        $data = ['some_data' => 'Hello, Dashboard!'];

        $userId = Auth::id(); // Get current logged-in user's ID

        return view('dashboard', [
            'totalBookings' => Booking::where('user_id', $userId)->count(),
            'todaysBookings' => Booking::where('user_id', $userId)->whereDate('created_at', now())->count(),
            'totalUsers' => User::count(), // Optional: can be removed if not needed
            'totalPackages' => Package::count(),
            'recentBookings' => Booking::where('user_id', $userId)->latest()->take(5)->get()
        ]);
    }


}
