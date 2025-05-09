@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Fare Details</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Pickup:</strong> {{ $pickup }}</p>
            <p><strong>Drop:</strong> {{ $drop }}</p>
            <p><strong>Distance:</strong> {{ $distance }} km</p>
            <p><strong>Duration:</strong> {{ $duration }} hrs</p>
            <p><strong>Package:</strong> {{ $package->name }} ({{ $package->max_km }} km / {{ $package->max_hours }} hr)</p>
            <p><strong>Extra KM:</strong> {{ $extra_km }} km (₹{{ $extra_km * $package->extra_km_price }})</p>
            <p><strong>Extra HR:</strong> {{ $extra_hr }} hr (₹{{ $extra_hr * $package->extra_hr_price }})</p>
            <h4>Total Fare: ₹{{ $final_price }}</h4>
        </div>
    </div>

    <form method="POST" action="{{ route('taxi.book') }}">
        @csrf
        <input type="hidden" name="pickup" value="{{ $pickup }}">
        <input type="hidden" name="drop" value="{{ $drop }}">
        <input type="hidden" name="package_id" value="{{ $package->id }}">
        <input type="hidden" name="distance" value="{{ $distance }}">
        <input type="hidden" name="duration" value="{{ $duration }}">
        <input type="hidden" name="extra_km" value="{{ $extra_km }}">
        <input type="hidden" name="extra_hr" value="{{ $extra_hr }}">
        <input type="hidden" name="final_price" value="{{ $final_price }}">
        <button type="submit" class="btn btn-success mt-3">Confirm Booking</button>
    </form>
</div>
@endsection
