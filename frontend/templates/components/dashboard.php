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

<!-- Room Management Section -->
<div class="container mx-auto p-6 mt-6">
    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Room Management</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Add Room Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
            <div onclick="openAddRoomModal()" class="w-full cursor-pointer">
                <div class="flex flex-col items-center">
                    <i class="bx bx-plus-circle text-5xl mb-4" style="color: #D885A3;"></i>
                    <h4 class="text-xl font-medium text-gray-600">Add New Room</h4>
                    <p class="text-sm text-gray-500 mt-2">Manage and add new rooms to the system</p>
                </div>
            </div>
        </div>

        <!-- Manage Rooms Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
            <div onclick="openRoomListModal()" class="w-full cursor-pointer">
                <div class="flex flex-col items-center">
                    <i class="bx bx-door-open text-5xl mb-4" style="color: #D885A3;"></i>
                    <h4 class="text-xl font-medium text-gray-600">Manage Rooms</h4>
                    <p class="text-sm text-gray-500 mt-2">Update, edit, or remove existing rooms</p>
                </div>
            </div>
        </div>

        <!-- Room Occupancy Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
            <div class="flex flex-col items-center">
                <i class="bx bx-bar-chart text-5xl mb-4" style="color: #D885A3;"></i>
                <h4 class="text-xl font-medium text-gray-600">Room Occupancy</h4>
                <p id="roomOccupancy" class="text-3xl font-bold mt-2" style="color: #D885A3;">...</p>
            </div>
        </div>
    </div>
</div>

<!-- Room Management Modals -->
<div id="modalContainer" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center overflow-y-auto py-8 max-h-screen w-full">
    <div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <!-- Add Room Modal -->
        <div id="addRoomModal" class="bg-white p-8 rounded-lg shadow-xl w-96 self-center mx-auto hidden">
            <h2 class="text-2xl font-bold mb-6 text-center">Add New Room</h2>
            <form id="addRoomForm" action="#" method="post" class="space-y-4">
                <input type="text" name="roomID" placeholder="Room ID" required 
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select name="type" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Room Type</option>
                    <option value="class">Classroom</option>
                    <option value="lab">Laboratory</option>
                    <option value="meeting">Meeting Room</option>
                </select>
                <input type="number" name="capacity" placeholder="Capacity" required 
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" name="floor" placeholder="Floor" required 
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <label class="flex items-center">
                    <input type="checkbox" name="isAvailable" value="1" class="mr-2">
                    Available
                </label>
                <div class="flex justify-between">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Room</button>
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Room List Modal -->
        <div id="roomListModal" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl hidden">
            <h2 class="text-2xl font-bold mb-6 text-center">Room List</h2>
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-md rounded">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Room ID</th>
                            <th class="py-3 px-6 text-left">Type</th>
                            <th class="py-3 px-6 text-left">Capacity</th>
                            <th class="py-3 px-6 text-left">Floor</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($room = $roomResult->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo htmlspecialchars($room['roomID']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($room['type']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($room['capacity']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($room['floor']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo $room['isAvailable'] ? 'Available' : 'Unavailable'; ?></td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <button onclick="editRoom('<?php echo htmlspecialchars($room['roomID']); ?>')" class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button onclick="deleteRoom('<?php echo htmlspecialchars($room['roomID']); ?>')" class="w-4 transform hover:text-red-500 hover:scale-110">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Close</button>
            </div>
        </div>
    </div>
</div>

</html>
<script src="../../js/modal.js"></script>
<?php } ?>