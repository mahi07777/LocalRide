<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class TaxiController extends Controller
{
    public function showSearchForm()
    {
        $packages = Package::all();
        return view('taxi.search', compact('packages'));
    }

    public function calculateFare(Request $request)
    {
        $request->validate([
            'pickup' => 'required|string',
            'drop' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'distance' => 'required|numeric',
            'duration' => 'required|numeric',
        ]);

        $package = Package::findOrFail($request->package_id);

        $extra_km = max(0, $request->distance - $package->max_km);
        $extra_hr = max(0, $request->duration - $package->max_hours);

        $final_price = $package->base_price +
                       ($extra_km * $package->extra_km_price) +
                       ($extra_hr * $package->extra_hr_price);

        return view('taxi.fare', [
            'pickup' => $request->pickup,
            'drop' => $request->drop,
            'distance' => $request->distance,
            'duration' => $request->duration,
            'package' => $package,
            'extra_km' => $extra_km,
            'extra_hr' => $extra_hr,
            'final_price' => $final_price
        ]);
    }

    public function bookTaxi(Request $request)
    {
        $request->validate([
            'pickup' => 'required|string',
            'drop' => 'required|string',
            'package_id' => 'required|exists:packages,id',
            'distance' => 'required|numeric',
            'duration' => 'required|numeric',
            'extra_km' => 'required|numeric',
            'extra_hr' => 'required|numeric',
            'final_price' => 'required|numeric'
        ]);

        Booking::create([
            'user_id' => Auth::id(),
            'pickup_address' => $request->pickup,
            'drop_address' => $request->drop,
            'package_id' => $request->package_id,
            'distance_km' => $request->distance,
            'duration_hr' => $request->duration,
            'extra_km' => $request->extra_km,
            'extra_hr' => $request->extra_hr,
            'final_price' => $request->final_price
        ]);

        return redirect('/bookings')->with('success', 'Taxi booked successfully.');
    }

    public function listBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())->with('package')->latest()->get();
        return view('taxi.bookings', compact('bookings'));
    }
}

