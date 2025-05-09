@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Form Section -->
        <div class="col-md-4">
            <h3 class="mb-3"><b>Search Taxi (Local Tour)</b></h3>

            <form method="POST" id="fareForm">
                @csrf

                <div class="mb-3">
                <input type="text" class="form-control" id="pickup" name="pickup" placeholder="Pickup Location" list="pickupSuggestions" required>
                <datalist id="pickupSuggestions"></datalist>
                </div>

                <div class="mb-3">
                <input type="text" class="form-control" id="drop" name="drop" placeholder="Drop Location" list="dropSuggestions" required>
                <datalist id="dropSuggestions"></datalist>
                </div>

                <div class="mb-3">
                    <label for="package">Select Package</label>
                    <select class="form-control" name="package_id" id="package_id" required>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}"
                                data-price="{{ $package->base_price }}"
                                data-km="{{ $package->km_limit }}"
                                data-hr="{{ $package->hr_limit }}">
                                {{ $package->name }} (₹{{ $package->base_price }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Calculate Fare</button>
            </form>

            <!-- Fare Details Table -->
            <div id="fareDetails" class="mt-4" style="display: none;">
                <h3 class="mb-3"><b>Trip Details</b></h3>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Detail</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Distance</td><td><span id="showDistance"></span> km</td></tr>
                        <tr><td>Duration</td><td><span id="showDuration"></span> hrs</td></tr>
                        <tr><td>Extra KM</td><td><span id="showExtraKm"></span> km</td></tr>
                        <tr><td>Extra Hours</td><td><span id="showExtraHr"></span> hrs</td></tr>
                        <tr><td>Estimated Fare</td><td>₹<span id="showFare"></span></td></tr>
                    </tbody>
                </table>

                <!-- Book Ride Form -->
                <form method="POST" action="{{ route('taxi.book') }}">
                    @csrf
                    <input type="hidden" name="pickup" id="book_pickup">
                    <input type="hidden" name="drop" id="book_drop">
                    <input type="hidden" name="package_id" id="book_package">
                    <input type="hidden" name="distance" id="book_distance">
                    <input type="hidden" name="duration" id="book_duration">
                    <input type="hidden" name="extra_km" id="book_extra_km">
                    <input type="hidden" name="extra_hr" id="book_extra_hr">
                    <input type="hidden" name="final_price" id="book_final_price">

                    <button type="submit" class="btn btn-success">Book Ride</button>
                </form>
            </div>
        </div>

        <!-- Map Section -->
        <div class="col-md-8">
            <div id="map" style="height: 400px; margin-top: 20px;"></div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<script>

async function fetchLocationSuggestions(query, datalistId) {
    if (query.length < 3) return; 
    try {
        const bbox = '&viewbox=73.75,18.65,73.95,18.45&bounded=1';

        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1&countrycodes=in${bbox}`);

        const data = await res.json();

        const datalist = document.getElementById(datalistId);
        datalist.innerHTML = '';

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.display_name;
            datalist.appendChild(option);
        });

    } catch (error) {
        console.error('Location suggestion error:', error);
    }
}
        // Attach to input events
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('pickup').addEventListener('input', function () {
                fetchLocationSuggestions(this.value, 'pickupSuggestions');
            });

            document.getElementById('drop').addEventListener('input', function () {
                fetchLocationSuggestions(this.value, 'dropSuggestions');
            });
        });


    const map = L.map('map').setView([19.7515, 75.7139], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    let routeLine;

    async function getLatLngFromAddress(address) {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
        const data = await res.json();
        if (!data.length) throw new Error("Location not found");
        return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
    }

    async function getRouteData(startCoords, endCoords) {
        const response = await fetch("https://api.openrouteservice.org/v2/directions/driving-car/geojson", {
            method: "POST",
            headers: {
                "Authorization": "5b3ce3597851110001cf62480716c341305c4b13b3b138d1e7ee05b2",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                coordinates: [
                    [startCoords.lng, startCoords.lat],
                    [endCoords.lng, endCoords.lat]
                ]
            })
        });
        const geo = await response.json();
        const summary = geo.features[0].properties.summary;
        return {
            distance: summary.distance / 1000, // km
            duration: summary.duration / 3600, // hr
            geometry: geo.features[0].geometry
        };
    }

    async function prepareDistanceData() {
    const pickup = document.getElementById('pickup').value;
    const drop = document.getElementById('drop').value;
    const selectedPackage = document.getElementById('package_id');
    const basePrice = parseFloat(selectedPackage.options[selectedPackage.selectedIndex].getAttribute('data-price')) || 0;
    const includedKm = parseFloat(selectedPackage.options[selectedPackage.selectedIndex].getAttribute('data-km')) || 0;
    const includedHr = parseFloat(selectedPackage.options[selectedPackage.selectedIndex].getAttribute('data-hr')) || 0;

    try {
        const pickupCoords = await getLatLngFromAddress(pickup);
        const dropCoords = await getLatLngFromAddress(drop);
        const { distance, duration, geometry } = await getRouteData(pickupCoords, dropCoords);

        const extraKm = Math.max(0, distance - includedKm);
        const extraHr = Math.max(0, duration - includedHr);
        const extraKmCharge = extraKm * 10;
        const extraHrCharge = extraHr * 50;
        const finalFare = (basePrice + extraKmCharge + extraHrCharge).toFixed(2);

        document.getElementById('showDistance').innerText = distance.toFixed(2);
        document.getElementById('showDuration').innerText = duration.toFixed(2);
        document.getElementById('showExtraKm').innerText = extraKm.toFixed(2);
        document.getElementById('showExtraHr').innerText = extraHr.toFixed(2);
        document.getElementById('showFare').innerText = finalFare;
        document.getElementById('fareDetails').style.display = 'block';

        document.getElementById('book_pickup').value = pickup;
        document.getElementById('book_drop').value = drop;
        document.getElementById('book_package').value = selectedPackage.value;
        document.getElementById('book_distance').value = distance.toFixed(2);
        document.getElementById('book_duration').value = duration.toFixed(2);
        document.getElementById('book_extra_km').value = extraKm.toFixed(2);
        document.getElementById('book_extra_hr').value = extraHr.toFixed(2);
        document.getElementById('book_final_price').value = finalFare;

        if (routeLine) map.removeLayer(routeLine);
        routeLine = L.geoJSON(geometry, { color: 'blue' }).addTo(map);
        map.setView([pickupCoords.lat, pickupCoords.lng], 10);
        L.marker([pickupCoords.lat, pickupCoords.lng]).addTo(map).bindPopup("Pickup").openPopup();
        L.marker([dropCoords.lat, dropCoords.lng]).addTo(map).bindPopup("Drop");

    } catch (error) {
        alert("Error: " + error.message);
    }
}

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('fareForm').addEventListener('submit', function (e) {
            e.preventDefault();
            prepareDistanceData();
        });
    });
</script>
@endsection
