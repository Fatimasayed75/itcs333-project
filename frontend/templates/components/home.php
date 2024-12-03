<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/room-model.php';
require_once '../../../backend/utils/helpers.php';

$id = isAuthorized();

$userModel = new UserModel($pdo);
$user = $userModel->getUserByID($id);

$roomModel = new RoomModel($pdo);
$rooms = $roomModel->getAllRooms();
usort($rooms, function ($a, $b) {
  return $a['roomID'] <=> $b['roomID'];
});

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
      <!-- Search Bar -->
      <div class="relative">
        <input type="text" id="roomSearch"
          class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Search rooms..." />
        <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-3 right-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
          fill="currentColor">
          <path fill-rule="evenodd"
            d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 11-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z"
            clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <!-- Rooms Container -->
    <div id="roomsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <!-- Dynamically Generated Room Cards (Card View) -->
      <?php foreach ($rooms as $room): ?>
        <div
          class="room-card relative p-4 bg-blue-100 rounded-lg shadow transform transition duration-300 hover:scale-105 hover:shadow-xl"
          data-room-type="<?php echo strtolower($room['type']); ?>" data-room-capacity="<?php echo $room['capacity']; ?>">
          <!-- Icon in Top-Right Corner -->
          <div class="absolute top-2 right-2">
            <?php if ($room['isAvailable'] == 1): ?>
              <i class="fa fa-check-circle text-green-500 text-xl" title="Available"></i>
            <?php else: ?>
              <i class="fa fa-times-circle text-red-500 text-xl" title="Not Available"></i>
            <?php endif; ?>
          </div>

          <!-- Room Details -->
          <h3 class="text-lg font-semibold text-gray-800"><?php echo $room['roomID']; ?></h3>
          <div class="flex justify-around items-center text-sm text-gray-600 mt-2">
            <span>Type: <?php echo htmlspecialchars($room['type']); ?></span>
            <span>Capacity: <?php echo htmlspecialchars($room['capacity']); ?></span>
          </div>
          <div>
            <img src="https://placehold.co/600x400?text=Image+For+<?php echo htmlspecialchars($room['roomID']); ?>"
              class="mt-5 rounded-lg">
          </div>
        </div>
      <?php endforeach; ?>
    </div>


    <!-- Dynamically Generated Room Squares (Grid View) -->
    <!-- Floor Navigation Section with Arrows -->
    <div class="flex justify-center space-x-8 mb-4 hidden">
      <button id="prevFloorBtn" class="floor-nav-btn text-3xl">&lt;</button>
      <span id="currentFloor" class="text-xl">Floor 1</span>
      <button id="nextFloorBtn" class="floor-nav-btn text-3xl">&gt;</button>
    </div>

    <div id="grid-rooms" class="">
      <?php foreach ($rooms as $room): ?>
        <div class="room-square bg-blue-100 rounded-lg shadow p-4 relative max-w-xs"
          data-room-id="<?php echo htmlspecialchars($room['roomID']); ?>"
          data-room-floor="<?php echo htmlspecialchars($room['floor']); ?>">

          <div
            class="room-id-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 bg-gray-800 text-white text-sm rounded hidden">
            <?php echo htmlspecialchars($room['roomID']); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  </div>
</body>