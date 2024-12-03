<?php
date_default_timezone_set('Asia/Bahrain');
?>

<!-- Simple dashboard structure to test -->
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-gray-800 mb-8">Admin Dashboard</h1>

    <!-- Statistics Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Booking Count -->
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
            <h3 class="text-xl font-medium text-gray-600">Total Bookings</h3>
            <p id="bookingCount" class="text-3xl font-bold text-orange-600">Loading...</p>
        </div>

        <!-- Most Booked Room -->
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
            <h3 class="text-xl font-medium text-gray-600">Most Booked Room</h3>
            <p id="mostBookedRoom" class="text-3xl font-bold text-purple-600">Loading...</p>
        </div>
    </div>

    <!-- Booking Statistics Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 mb-12">
        <h3 class="text-xl font-medium text-gray-600 mb-4">Booking Statistics by Month</h3>
        <canvas id="bookingChart" width="400" height="200"></canvas>
    </div>

    <!-- Booking Statistics by Department (Pie Chart) -->
    <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
        <h3 class="text-xl font-medium text-gray-600 mb-4">Booking Statistics by Department</h3>
        <canvas id="departmentChart" width="400" height="400"></canvas>
    </div>
</div>
