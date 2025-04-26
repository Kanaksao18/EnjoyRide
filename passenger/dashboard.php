<?php
require_once '../../config/config.php';
requireAuth();
if (getUserRole() !== 'passenger') {
    header('Location: /');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CabShare - Passenger Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places"></script>
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
                                <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-md">
                                    <i class="fas fa-home w-5"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="book_ride.php" class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100 rounded-md">
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
                    <h2 class="text-2xl font-bold mb-6">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-lg p-4 flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-car text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Rides</p>
                                <h3 class="text-xl font-semibold">24</h3>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Completed</p>
                                <h3 class="text-xl font-semibold">22</h3>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4 flex items-center space-x-4">
                            <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-wallet text-purple-500"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Spent</p>
                                <h3 class="text-xl font-semibold">₹1,245</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Quick Book a Ride</h3>
                        <div id="map" class="h-64 mb-4 rounded-lg"></div>
                        
                        <form id="quickBookForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="pickup" class="block text-sm font-medium text-gray-700">Pickup Location</label>
                                    <input type="text" id="pickup" name="pickup" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="dropoff" class="block text-sm font-medium text-gray-700">Dropoff Location</label>
                                    <input type="text" id="dropoff" name="dropoff" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit"
                                        class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Find Rides
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Recent Rides</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dropoff</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fare</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">Today, 10:30 AM</td>
                                        <td class="px-6 py-4 whitespace-nowrap">MG Road</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Airport</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rajesh K.</td>
                                        <td class="px-6 py-4 whitespace-nowrap">₹250</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">Yesterday, 5:15 PM</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Central Mall</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Home</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Priya M.</td>
                                        <td class="px-6 py-4 whitespace-nowrap">₹180</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">Yesterday, 8:45 AM</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Home</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Office</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Amit S.</td>
                                        <td class="px-6 py-4 whitespace-nowrap">₹150</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Google Maps
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 12.9716, lng: 77.5946 }, // Default to Bangalore
                zoom: 12,
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
                
                // Center the map on the selected location
                map.setCenter(place.geometry.location);
                new google.maps.Marker({
                    map,
                    position: place.geometry.location,
                    title: "Pickup Location",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
                    },
                });
            });
            
            dropoffAutocomplete.addListener("place_changed", () => {
                const place = dropoffAutocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                
                new google.maps.Marker({
                    map,
                    position: place.geometry.location,
                    title: "Dropoff Location",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
                    },
                });
            });
        }
        
        // Initialize the map when the page loads
        window.initMap = initMap;
        
        // Handle quick book form submission
        document.getElementById("quickBookForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const pickup = document.getElementById("pickup").value;
            const dropoff = document.getElementById("dropoff").value;
            
            if (!pickup || !dropoff) {
                alert("Please enter both pickup and dropoff locations");
                return;
            }
            
            // Redirect to book ride page with parameters
            window.location.href = `book_ride.php?pickup=${encodeURIComponent(pickup)}&dropoff=${encodeURIComponent(dropoff)}`;
        });
    </script>
</body>
</html>