<x-app-layout>
{{--
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
</x-slot>
--}}


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    <p>{{ $some_data ?? '' }}</p>  </div>
            </div>
        </div>
    </div>

@section('content')
<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bookings</h5>
                    <p class="card-text fs-4">{{ $totalBookings }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Today’s Bookings</h5>
                    <p class="card-text fs-4">{{ $todaysBookings }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Available Packages</h5>
                    <p class="card-text fs-4">{{ $totalPackages }}</p>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Bookings</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pickup</th>
                        <th>Drop</th>
                        <th>Distance (km)</th>
                        <th>Fare (₹)</th>
                        <th>Booked At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $index => $booking)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $booking->pickup_address }}</td>
                            <td>{{ $booking->drop_address }}</td>
                            <td>{{ number_format($booking->distance_km, 2) }}</td>
                            <td>₹{{ number_format($booking->final_price, 2) }}</td>
                            <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
   

</x-app-layout>



