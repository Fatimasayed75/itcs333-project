<?php
date_default_timezone_set('Asia/Bahrain');
?>

<div class="adminDash home-page">
    <div class="container mx-auto p-6 mt-6">
        <h1 class="text-3xl font-semibold text-gray-800 mb-8">Admin Dashboard</h1>

        <!-- Statistics Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Booking Count -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <h3 class="text-xl font-medium text-gray-600">Total Bookings</h3>
                <p id="bookingCount" class="text-3xl font-bold text-orange-600">...</p>
            </div>

            <!-- Most Booked Room -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <h3 class="text-xl font-medium text-gray-600">Most Booked Room</h3>
                <p id="mostBookedRoom" class="text-3xl font-bold text-purple-600">...</p>
            </div>

            <!-- Most Booked Room -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <h3 class="text-xl font-medium text-gray-600">Total Users</h3>
                <p id="mostBookedRoom" class="text-3xl font-bold text-purple-600">...</p>
            </div>

            <!-- Most Booked Room -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                <h3 class="text-xl font-medium text-gray-600">New Feedbacks</h3>
                <p id="mostBookedRoom" class="text-3xl font-bold text-purple-600">...</p>
            </div>
        </div>

        <h3 class="text-xl font-medium text-gray-600 mb-4">Booking Statistics</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Line Chart -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
                <h3 class="text-xl font-medium text-gray-600 mb-4">Booking Rate by Month</h3>
                <div id="chart-container" class="w-full h-72 flex justify-center items-center">
                    <canvas id="bookingChart" class="w-full h-full"></canvas>
                </div>
            </div>

            
            <!-- Pie Chart -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
                <h3 class="text-xl font-medium text-gray-600 mb-4">Booking Rate by Department</h3>
                <div id="chart-container" class="w-full h-72 flex justify-center items-center">
                    <canvas id="departmentChart" class="w-full h-full"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>
