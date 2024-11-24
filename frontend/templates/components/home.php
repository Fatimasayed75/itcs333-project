<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/room-model.php';
require_once '../../../backend/utils/helpers.php';

$id = isAuthorized();

$userModel = new UserModel($pdo);
$user = $userModel->getUserByID($id);

$roomModel = new RoomModel($pdo);
$rooms = $roomModel->getAllRooms();
?>

<body class="bg-gray-100 min-h-screen mr-5">
  <!-- Welcome Message -->
  <div class="text-left pb-6 pt-20 sm:pt-15 lg:pt-5 md:pt-10">
    <h1 class="text-xl sm:text-3xl lg:text-4xl font-bold text-gray-800 welcome-message">
      Welcome, <?php echo $user["firstName"] . " " . $user["lastName"] . "!"; ?>
    </h1>
  </div>

  <!-- Reminders Section -->
  <div class="w-full bg-white shadow-lg py-6 px-4 sm:px-8 lg:px-16">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Important</h2>
    <div class="flex flex-wrap gap-4 justify-start overflow-x-auto">
      <!-- Reminder Card 1 -->
      <div class="flex-1 min-w-[250px] max-w-sm bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-800">Room S40-021</h3>
        <p class="text-sm text-gray-600">Date: 20th Nov 2024</p>
        <p class="text-sm text-gray-600">Time: 10:00 AM</p>
        <a href="#" class="text-sm text-blue-600 underline">View Details</a>
      </div>
      <!-- Reminder Card 2 -->
      <div class="flex-1 min-w-[250px] max-w-sm bg-green-50 border-l-4 border-green-500 rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-800">Request Accepted</h3>
        <p class="text-sm text-gray-600">Date: 22nd Nov 2024</p>
        <p class="text-sm text-gray-600">Time: 2:00 PM</p>
        <a href="#" class="text-sm text-blue-600 underline">View Details</a>
      </div>

      <!-- Reminder Card 3 -->
      <div class="flex-1 min-w-[250px] max-w-sm bg-red-50 border-l-4 border-red-500 rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-800">Request Rejected</h3>
        <p class="text-sm text-gray-600">Date: 23rd Nov 2024</p>
        <p class="text-sm text-gray-600">Time: 6:00 PM</p>
        <a href="#" class="text-sm text-blue-600 underline">View Details</a>
      </div>
    </div>
  </div>

  <!-- Browse All Rooms Section -->
  <div class="w-full bg-white shadow-lg my-8 py-6 px-4 sm:px-8 lg:px-16">
    <div class="flex space-x-12 mb-6">
      <button id="cardViewBtn" class="view-toggle-btn">Card View</button>
      <button id="gridViewBtn" class="view-toggle-btn">Grid View</button>
    </div>

    <!-- Rooms Container -->
    <div id="roomsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

      <!-- Dynamically Generated Room Cards (Card View) -->
      <?php foreach ($rooms as $room): ?>
        <div class="room-card p-4 bg-blue-100 rounded-lg shadow" data-room-id="<?php echo $room['roomID'];?>">
          <h3 class="text-lg font-semibold text-gray-800"><?php echo $room['roomID']; ?></h3>
          <div class="flex justify-between items-center text-sm text-gray-600">
            <span>Type: <?php echo $room['type']; ?></span>
            <span>Capacity: <?php echo $room['capacity']; ?></span>

            <span>
                <?php if ($room['isAvailable'] == 1): ?>
                <!-- <i class="fa fa-check-circle text-green-500 text-xl" title="Available"></i> -->
                <?php else: ?>
                <!-- <i class="fa fa-times-circle text-red-500 text-xl" title="Not Available"></i> -->
                <?php endif; ?>
            </span>
            
          </div>
          <div>
            <img src="https://placehold.co/600x400?text=Image+For+<?php echo $room['roomID']; ?>" class="mt-5">
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Dynamically Generated Room Squares (Grid View) -->
      <?php foreach ($rooms as $room): ?>
        <div class="room-square p-6 bg-blue-100 rounded-lg shadow text-center hidden" data-room-id="<?php echo $room['roomID'];?>">
          <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($room['roomID']); ?></h3>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</body>