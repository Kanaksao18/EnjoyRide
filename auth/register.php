<?php
require_once '../config/config.php';

if (isLoggedIn()) {
    redirectBasedOnRole();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $role = 'passenger'; // Default role
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);

    // Basic validation
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username or email already exists';
            } else {
                // Insert new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, first_name, last_name, phone) VALUES (:username, :email, :password, :role, :first_name, :last_name, :phone)");
                
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'role' => $role,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone
                ]);
                
                $success = 'Registration successful! You can now login.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CabShare - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">CabShare</h1>
            <p class="text-gray-600 mt-2">Create your account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>
        
        <form id="registerForm" method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p id="firstNameError" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p id="lastNameError" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
            </div>
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p id="usernameError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p id="emailError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" id="phone" name="phone" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p id="phoneError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p id="passwordError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p id="confirmPasswordError" class="text-red-500 text-xs mt-1 hidden"></p>
            </div>
            
            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-700">
                    I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a>
                </label>
            </div>
            
            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Register
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="/auth/login.php" class="font-medium text-blue-600 hover:text-blue-500">Login</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const terms = document.getElementById('terms');
            
            // Reset errors
            document.querySelectorAll('[id$="Error"]').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Validate first name
            if (firstName.value.trim() === '') {
                document.getElementById('firstNameError').textContent = 'First name is required';
                document.getElementById('firstNameError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate last name
            if (lastName.value.trim() === '') {
                document.getElementById('lastNameError').textContent = 'Last name is required';
                document.getElementById('lastNameError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate username
            if (username.value.trim() === '') {
                document.getElementById('usernameError').textContent = 'Username is required';
                document.getElementById('usernameError').classList.remove('hidden');
                isValid = false;
            } else if (username.value.length < 4) {
                document.getElementById('usernameError').textContent = 'Username must be at least 4 characters';
                document.getElementById('usernameError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate email
            if (email.value.trim() === '') {
                document.getElementById('emailError').textContent = 'Email is required';
                document.getElementById('emailError').classList.remove('hidden');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                document.getElementById('emailError').textContent = 'Invalid email format';
                document.getElementById('emailError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate phone
            if (phone.value.trim() === '') {
                document.getElementById('phoneError').textContent = 'Phone number is required';
                document.getElementById('phoneError').classList.remove('hidden');
                isValid = false;
            } else if (!/^\d{10,15}$/.test(phone.value)) {
                document.getElementById('phoneError').textContent = 'Invalid phone number';
                document.getElementById('phoneError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate password
            if (password.value.trim() === '') {
                document.getElementById('passwordError').textContent = 'Password is required';
                document.getElementById('passwordError').classList.remove('hidden');
                isValid = false;
            } else if (password.value.length < 6) {
                document.getElementById('passwordError').textContent = 'Password must be at least 6 characters';
                document.getElementById('passwordError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate confirm password
            if (confirmPassword.value.trim() === '') {
                document.getElementById('confirmPasswordError').textContent = 'Please confirm your password';
                document.getElementById('confirmPasswordError').classList.remove('hidden');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                document.getElementById('confirmPasswordError').classList.remove('hidden');
                isValid = false;
            }
            
            // Validate terms
            if (!terms.checked) {
                isValid = false;
                alert('You must agree to the terms and conditions');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>