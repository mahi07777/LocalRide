@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Bookings</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Pickup Address</th>
                <th>Drop Address</th>
                <th>Package</th>
                <th>Total Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->pickup_address }}</td>
                <td>{{ $booking->drop_address }}</td>
                <td>{{ $booking->package->name }}</td>
                <td>₹{{ number_format($booking->final_price, 2) }}</td>
                <td>{{ $booking->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
