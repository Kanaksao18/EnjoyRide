<?php
require_once '../../config/config.php';
requireAuth();
if (getUserRole() !== 'passenger') {
    header('Location: /');
    exit();
}

// In a real application, you would fetch ride details from the database
$rideId = $_GET['ride_id'] ?? 0;
$amount = $_GET['amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CabShare - Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
                                <a href="#" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-md">
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
                    <h2 class="text-2xl font-bold mb-6">Complete Payment</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4">Ride Summary</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ride ID</span>
                                        <span class="font-medium">#<?php echo htmlspecialchars($rideId); ?></span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Driver</span>
                                        <span class="font-medium">Rajesh K. (4.8 ★)</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Vehicle</span>
                                        <span class="font-medium">Maruti Suzuki Swift (KA01AB1234)</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pickup</span>
                                        <span class="font-medium">MG Road, Bangalore</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Dropoff</span>
                                        <span class="font-medium">Kempegowda International Airport</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Distance</span>
                                        <span class="font-medium">36.5 km</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Duration</span>
                                        <span class="font-medium">52 min</span>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 my-2"></div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-semibold">Total Amount</span>
                                        <span class="text-blue-600 font-bold">₹<?php echo htmlspecialchars($amount); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold mb-4">Payment Method</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-blue-500">
                                        <input type="radio" id="razorpay" name="payment_method" value="razorpay" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                        <label for="razorpay" class="flex-1 cursor-pointer">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium">Razorpay</span>
                                                <img src="https://razorpay.com/assets/razorpay-glyph.svg" alt="Razorpay" class="h-6">
                                            </div>
                                            <p class="text-sm text-gray-500">Pay using UPI, Credit/Debit Card, Net Banking</p>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-blue-500">
                                        <input type="radio" id="wallet" name="payment_method" value="wallet" class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                        <label for="wallet" class="flex-1 cursor-pointer">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium">CabShare Wallet</span>
                                                <i class="fas fa-wallet text-blue-500"></i>
                                            </div>
                                            <p class="text-sm text-gray-500">Balance: ₹0.00</p>
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-blue-500">
                                        <input type="radio" id="cod" name="payment_method" value="cod" class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                        <label for="cod" class="flex-1 cursor-pointer">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium">Cash on Delivery</span>
                                                <i class="fas fa-money-bill-wave text-green-500"></i>
                                            </div>
                                            <p class="text-sm text-gray-500">Pay cash to the driver at the end of the ride</p>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <button id="payButton" class="w-full px-4 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                        Pay ₹<?php echo htmlspecialchars($amount); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-blue-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold mb-4">Need Help?</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-phone-alt text-blue-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Call Us</h4>
                                            <p class="text-sm text-gray-600">+91 9876543210</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-envelope text-blue-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Email Us</h4>
                                            <p class="text-sm text-gray-600">support@cabshare.com</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-comment-alt text-blue-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium">Live Chat</h4>
                                            <p class="text-sm text-gray-600">Available 24/7</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 bg-green-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold mb-4">Promo Code</h3>
                                
                                <div class="flex space-x-2">
                                    <input type="text" placeholder="Enter promo code" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                    <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Apply</button>
                                </div>
                                
                                <div class="mt-4 text-sm text-gray-600">
                                    <p>Apply promo code to get discounts on your ride.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payButton').addEventListener('click', function() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === 'razorpay') {
                initiateRazorpayPayment();
            } else if (paymentMethod === 'wallet') {
                alert('Insufficient balance in your wallet. Please choose another payment method.');
            } else if (paymentMethod === 'cod') {
                confirmCashPayment();
            }
        });
        
        function initiateRazorpayPayment() {
            const amount = <?php echo $amount * 100; ?>; // Razorpay expects amount in paise
            const options = {
                key: '<?php echo RAZORPAY_KEY_ID; ?>',
                amount: amount,
                currency: 'INR',
                name: 'CabShare',
                description: 'Payment for Ride #<?php echo $rideId; ?>',
                image: 'https://example.com/your_logo.jpg',
                order_id: '', // This will be generated from your backend
                handler: function(response) {
                    // This function will be called after successful payment
                    alert('Payment successful! Payment ID: ' + response.razorpay_payment_id);
                    
                    // In a real app, you would submit this to your server for verification
                    // and then redirect to a success page
                    window.location.href = 'payment_success.php?payment_id=' + response.razorpay_payment_id;
                },
                prefill: {
                    name: '<?php echo $_SESSION['username']; ?>',
                    email: '<?php echo $_SESSION['email']; ?>',
                    contact: '9876543210'
                },
                notes: {
                    ride_id: '<?php echo $rideId; ?>'
                },
                theme: {
                    color: '#3B82F6'
                }
            };
            
            // In a real application, you would first create an order on your server
            // and then use the order ID here. For this example, we're simulating it.
            
            // Create a promise to simulate async order creation
            const promise = new Promise((resolve) => {
                // Simulate API call to create order
                setTimeout(() => {
                    resolve({ id: 'order_' + Math.random().toString(36).substr(2, 9) });
                }, 1000);
            });
            
            promise.then((order) => {
                options.order_id = order.id;
                const rzp = new Razorpay(options);
                rzp.open();
            });
        }
        
        function confirmCashPayment() {
            if (confirm('Are you sure you want to pay with cash? The driver will collect the payment at the end of the ride.')) {
                // In a real app, this would update the ride status in the database
                alert('Your ride has been confirmed! Pay ₹<?php echo $amount; ?> in cash to the driver.');
                window.location.href = 'payment_success.php?payment_method=cod';
            }
        }
    </script>
</body>
</html>