<?php
require_once '../../config/config.php';
requireAuth();
if (getUserRole() !== 'driver') {
    header('Location: /');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CabShare - Driver Dashboard</title>
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
                            <p class="text-gray-500 text-sm">Driver</p>
                        </div>
                    </div>
                    
                    <nav>
                        <ul class="space-y-2">
                            <li>
                                <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-md">
                                    <i class="fas fa-home w-5"></i>
                                    <span>Dashboard</span