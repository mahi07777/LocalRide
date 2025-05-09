<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // You can fetch data for your dashboard here (e.g., from the database)
        $data = ['some_data' => 'Hello, Dashboard!'];

        // Return the dashboard view (create this file if it doesn't exist)
        return view('dashboard', $data);
    }
}
