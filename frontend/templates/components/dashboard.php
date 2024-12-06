<?php

require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';
require_once '../../../backend/database/room-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/db-connection.php';

use Utils\Constants;

date_default_timezone_set('Asia/Bahrain');

$id = isAuthorized();

if ($id === Constants::ADMIN_USER_ID) {
    $userModel = new UserModel($pdo);
    $user = $userModel->getUserByID($id);

    // Fetch all rooms
    $roomQuery = "SELECT * FROM room";
    $roomResult = $pdo->query($roomQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/profile.css">
</head>

<div class="adminDash home-page">
    <div class="container mx-auto p-6 mt-6">
        <h1 class="text-3xl font-semibold text-gray-800 mb-8">Admin Dashboard</h1>

        <!-- Statistics Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Booking Count -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
                <h3 class="text-xl font-medium text-gray-600 text-center">Total Bookings</h3>
                <p id="bookingCount" class="text-3xl font-bold" style="color: #D885A3;">...</p>
            </div>

            <!-- Most Booked Room -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
                <h3 class="text-xl font-medium text-gray-600 text-center">Most Booked Room</h3>
                <p id="mostBookedRoom" class="text-3xl font-bold" style="color: #D885A3;">...</p>
            </div>

            <!-- Total Users -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
                <h3 class="text-xl font-medium text-gray-600 text-center">Total Users</h3>
                <p id="totalUsers" class="text-3xl font-bold" style="color: #D885A3;">...</p>
            </div>

            <!-- New Feedbacks -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
                <h3 class="text-xl font-medium text-gray-600 text-center">Recent Feedbacks</h3>
                <p id="newFeedbacks" class="text-3xl font-bold" style="color: #D885A3;">...</p>
            </div>
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

</html>
<script src="../../js/modal.js"></script>
<?php } ?>