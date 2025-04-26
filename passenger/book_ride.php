<?php
require_once '../../config/config.php';
requireAuth();
if (getUserRole() !== 'passenger') {
    header('Location: /');
    exit();
}

// Get pickup and dropoff from query parameters if available
$pickup = $_GET['pickup'] ?? '';
$dropoff = $_GET['dropoff'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CabShare - Book a Ride</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places,geometry"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include '../../includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar -->
            <div class="w-full md:w-1/4 lg:w-1/5">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                            <p class="text-gray-500 text-sm">Passenger</p>
                        </div>
                    </div>
                    
                    <nav>
                        <ul class="space-y-2">
                            <li>
                                <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 rounded-md">
                                    <i class="fas fa-home w-5"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="book_ride.php" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-md">
                                    <i class="fas fa-car w-5"></i>
                                    <span>Book a Ride</span>
                                </a>
                            </li>
                            <li>
                                <a href="ride_history.php" class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 rounded-md">
                                    <i class="fas fa-history w-5"></i>
                                    <span>Ride History</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 rounded-md">
                                    <i class="fas fa-wallet w-5"></i>
                                    <span>Payments</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 rounded-md">
                                    <i class="fas fa-cog w-5"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="w-full md:w-3/4 lg:w-4/5">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold mb-6">Book a Ride</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div id="map" class="h-96 rounded-lg mb-4"></div>
                            
                            <form id="rideBookingForm" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="pickup" class="block text-sm font-medium text-gray-700">Pickup Location</label>
                                        <input type="text" id="pickup" name="pickup" value="<?php echo htmlspecialchars($pickup); ?>" required
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label for="dropoff" class="block text-sm font-medium text-gray-700">Dropoff Location</label>
                                        <input type="text" id="dropoff" name="dropoff" value="<?php echo htmlspecialchars($dropoff); ?>" required
                                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="ride_type" class="block text-sm font-medium text-gray-700">Ride Type</label>
                                        <select id="ride_type" name="ride_type" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="standard">Standard</option>
                                            <option value="premium">Premium</option>
                                            <option value="pool">Pool (Shared)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="ride_time" class="block text-sm font-medium text-gray-700">When</label>
                                        <select id="ride_time" name="ride_time" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="now">Now</option>
                                            <option value="later">Schedule for later</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="scheduleSection" class="hidden">
                                    <label for="schedule_time" class="block text-sm font-medium text-gray-700">Schedule Time</label>
                                    <input type="datetime-local" id="schedule_time" name="schedule_time"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <button type="submit" id="findRidesBtn"
                                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                        Find Available Rides
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">Ride Estimate</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Distance</span>
                                        <span id="distanceEstimate" class="font-medium">-</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Duration</span>
                                        <span id="durationEstimate" class="font-medium">-</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Base Fare</span>
                                        <span id="baseFare" class="font-medium">-</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Distance Fare</span>
                                        <span id="distanceFare" class="font-medium">-</span>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 my-2"></div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-semibold">Total Fare</span>
                                        <span id="totalFare" class="text-blue-600 font-bold">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="availableRides" class="mt-6 hidden">
                                <h3 class="text-lg font-semibold mb-4">Available Rides</h3>
                                
                                <div class="space-y-4" id="ridesList">
                                    <!-- Rides will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map;
        let pickupMarker;
        let dropoffMarker;
        let directionsService;
        let directionsRenderer;
        let pickupPlace;
        let dropoffPlace;
        
        // Initialize Google Maps
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 12.9716, lng: 77.5946 }, // Default to Bangalore
                zoom: 12,
            });
            
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true,
                polylineOptions: {
                    strokeColor: '#3b82f6',
                    strokeOpacity: 0.8,
                    strokeWeight: 4,
                }
            });
            
            // Add autocomplete to pickup and dropoff fields
            const pickupInput = document.getElementById("pickup");
            const dropoffInput = document.getElementById("dropoff");
            
            const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput);
            const dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput);
            
            // Add markers when places are selected
            pickupAutocomplete.addListener("place_changed", () => {
                const place = pickupAutocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                
                pickupPlace = place;
                
                // Clear existing pickup marker
                if (pickupMarker) {
                    pickupMarker.setMap(null);
                }
                
                // Add new pickup marker
                pickupMarker = new google.maps.Marker({
                    map,
                    position: place.geometry.location,
                    title: "Pickup Location",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
                    },
                });
                
                // Update route if both places are selected
                if (pickupPlace && dropoffPlace) {
                    calculateRoute();
                }
            });
            
            dropoffAutocomplete.addListener("place_changed", () => {
                const place = dropoffAutocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                
                dropoffPlace = place;
                
                // Clear existing dropoff marker
                if (dropoffMarker) {
                    dropoffMarker.setMap(null);
                }
                
                // Add new dropoff marker
                dropoffMarker = new google.maps.Marker({
                    map,
                    position: place.geometry.location,
                    title: "Dropoff Location",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
                    },
                });
                
                // Update route if both places are selected
                if (pickupPlace && dropoffPlace) {
                    calculateRoute();
                }
            });
            
            // Show/hide schedule time based on selection
            document.getElementById("ride_time").addEventListener("change", function() {
                const scheduleSection = document.getElementById("scheduleSection");
                if (this.value === "later") {
                    scheduleSection.classList.remove("hidden");
                } else {
                    scheduleSection.classList.add("hidden");
                }
            });
        }
        
        // Calculate route between pickup and dropoff
        function calculateRoute() {
            if (!pickupPlace || !dropoffPlace) return;
            
            const request = {
                origin: pickupPlace.geometry.location,
                destination: dropoffPlace.geometry.location,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
            };
            
            directionsService.route(request, function(result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                    
                    // Update fare estimate
                    updateFareEstimate(result);
                }
            });
        }
        
        // Update fare estimate based on route
        function updateFareEstimate(directionsResult) {
            const route = directionsResult.routes[0];
            const leg = route.legs[0];
            
            // Calculate distance in km
            const distance = leg.distance.value / 1000;
            // Calculate duration in minutes
            const duration = leg.duration.value / 60;
            
            // Update UI
            document.getElementById("distanceEstimate").textContent = distance.toFixed(1) + " km";
            document.getElementById("durationEstimate").textContent = Math.ceil(duration) + " min";
            
            // Calculate fare (simple calculation)
            const baseFare = 40;
            const distanceFare = distance * 12;
            const totalFare = baseFare + distanceFare;
            
            // Update fare display
            document.getElementById("baseFare").textContent = "₹" + baseFare.toFixed(0);
            document.getElementById("distanceFare").textContent = "₹" + distanceFare.toFixed(0);
            document.getElementById("totalFare").textContent = "₹" + totalFare.toFixed(0);
        }
        
        // Handle form submission
        document.getElementById("rideBookingForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            if (!pickupPlace || !dropoffPlace) {
                alert("Please select both pickup and dropoff locations");
                return;
            }
            
            // Simulate finding available rides
            simulateAvailableRides();
        });
        
        // Simulate finding available rides
        function simulateAvailableRides() {
            const rideType = document.getElementById("ride_type").value;
            const ridesList = document.getElementById("ridesList");
            
            // Clear existing rides
            ridesList.innerHTML = "";
            
            // Sample ride data
            const rideData = [
                {
                    id: 1,
                    driver: "Rajesh K.",
                    rating: 4.8,
                    car: "Maruti Suzuki Swift",
                    license: "KA01AB1234",
                    eta: "5 min",
                    fare: "₹" + (Math.floor(Math.random() * 100) + 150),
                    image: "https://cdn.pixabay.com/photo/2019/07/07/14/03/fiat-4322521_640.jpg"
                },
                {
                    id: 2,
                    driver: "Priya M.",
                    rating: 4.9,
                    car: "Hyundai i20",
                    license: "KA02CD5678",
                    eta: "7 min",
                    fare: "₹" + (Math.floor(Math.random() * 100) + 150),
                    image: "https://cdn.pixabay.com/photo/2017/03/27/14/56/auto-2179220_640.jpg"
                },
                {
                    id: 3,
                    driver: "Amit S.",
                    rating: 4.7,
                    car: "Toyota Etios",
                    license: "KA03EF9012",
                    eta: "10 min",
                    fare: "₹" + (Math.floor(Math.random() * 100) + 150),
                    image: "https://cdn.pixabay.com/photo/2015/05/28/23/12/auto-788747_640.jpg"
                }
            ];
            
            // Add rides to the list
            rideData.forEach(ride => {
                const rideElement = document.createElement("div");
                rideElement.className = "bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-md transition-shadow";
                rideElement.innerHTML = `
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <img class="h-12 w-12 rounded-full object-cover" src="${ride.image}" alt="${ride.driver}">
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium">${ride.driver}</h4>
                                <span class="text-sm text-yellow-500">
                                    <i class="fas fa-star"></i> ${ride.rating}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">${ride.car} • ${ride.license}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-sm font-medium text-blue-600">${ride.fare}</span>
                                <span class="text-sm text-gray-500">ETA: ${ride.eta}</span>
                            </div>
                        </div>
                    </div>
                `;
                
                rideElement.addEventListener("click", function() {
                    confirmRide(ride);
                });
                
                ridesList.appendChild(rideElement);
            });
            
            // Show available rides section
            document.getElementById("availableRides").classList.remove("hidden");
        }
        
        // Confirm ride selection
        function confirmRide(ride) {
            if (confirm(`Confirm ride with ${ride.driver} for ${ride.fare}?`)) {
                // In a real app, this would initiate the ride booking process
                alert(`Your ride with ${ride.driver} has been confirmed!`);
                
                // Redirect to payment page (simulated)
                window.location.href = `payment.php?ride_id=${ride.id}&amount=${ride.fare.substring(1)}`;
            }
        }
        
        // Initialize the map when the page loads
        window.initMap = initMap;
    </script>
</body>
</html>