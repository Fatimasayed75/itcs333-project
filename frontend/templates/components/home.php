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
$CSrooms = [];
$ISrooms = [];
$CErooms = [];

// filter by department
foreach ($rooms as $room) {
  if ($room['department'] === 'CS') {
    $CSrooms[] = $room;
  } elseif ($room['department'] === 'IS') {
    $ISrooms[] = $room;
  } elseif ($room['department'] === 'CE') {
    $CErooms[] = $room;
  }
}
usort($CSrooms, function ($a, $b) {
  return $a['roomID'] <=> $b['roomID'];
});
usort($ISrooms, function ($a, $b) {
  return $a['roomID'] <=> $b['roomID'];
});
usort($CErooms, function ($a, $b) {
  return $a['roomID'] <=> $b['roomID'];
});

?>

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
$CSrooms = [];
$ISrooms = [];
$CErooms = [];

// filter by department
foreach ($rooms as $room) {
  if ($room['department'] === 'CS') {
    $CSrooms[] = $room;
  } elseif ($room['department'] === 'IS') {
    $ISrooms[] = $room;
  } elseif ($room['department'] === 'CE') {
    $CErooms[] = $room;
  }
}
usort($CSrooms, function ($a, $b) {
  return $a['roomID'] <=> $b['roomID'];
});
usort($ISrooms, function ($a, $b) {
  return $a['roomID'] <=> $b['roomID'];
});
usort($CErooms, function ($a, $b) {
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
    <div class="flex justify-between mb-6">
      <div class="flex space-x-4 sm:space-x-8 lg:space-x-12">
        <button id="cardViewBtn" class="view-toggle-btn">Card View</button>
        <button id="gridViewBtn" class="view-toggle-btn">Grid View</button>
      </div>
      <div class="flex justify-center items-center space-x-2">
        <!-- Search Bar -->
        <div class="relative w-full sm:max-w-md lg:max-w-lg mx-auto">
          <input type="text" id="roomSearch"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Search ..." />
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute top-3 right-3 h-5 w-5 text-gray-400"
            viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 11-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z"
              clip-rule="evenodd" />
          </svg>
        </div>


        <!-- Filter Icon Container -->
        <div class="relative" id="filterDiv">
          <!-- Filter Icon (always visible) -->
          <div id="filterIcon" class="cursor-pointer">
            <i class="fa fa-filter text-3xl p-2" aria-hidden="true"></i>
          </div>

          <!-- Clear Filter Icon (visible only if filters are applied) -->
          <div id="clearFilterIcon" class="absolute top-0 right-0 cursor-pointer hidden">
            <i class="fa fa-times-circle text-red-500 text-xs" aria-hidden="true"></i>
          </div>
        </div>

        <!-- Filter Modal -->
        <div id="filterBox"
          class="hidden fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-20">
          <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm text-center">

          </div>
        </div>
      </div>

    </div>

    <!-- Rooms Container -->
    <div id="roomsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <!-- Dynamically Generated Room Cards (Card View) -->
      <?php foreach ($rooms as $room): ?>
        <div
          class="room-card relative p-4 bg-blue-100 rounded-lg shadow transform transition duration-300 hover:scale-105 hover:shadow-xl"
          data-room-type="<?php echo strtolower($room['type']); ?>" data-room-capacity="<?php echo $room['capacity']; ?>"
          data-room-available="<?php echo $room['isAvailable']; ?>"
          data-room-id="<?php echo htmlspecialchars($room['roomID']); ?>" data-room-floor="<?php echo $room['floor']; ?>">
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
    <div id="grid-rooms" class="flex justify-around items-center mb-4 hidden">
      <div>
        <!-- Floor Nav -->
        <div class="flex justify-center space-x-8">
          <button id="prevFloorBtn" class="floor-nav-btn text-3xl">&lt;</button>
          <span id="currentFloor" class="text-xl">Ground Floor</span>
          <button id="nextFloorBtn" class="floor-nav-btn text-3xl">&gt;</button>
        </div>
        <div class="flex justify-center gap-12 p-4">
          <div id="IS-grid" class="dep grid grid-cols-2 gap-x-4 gap-y-2">
            <?php foreach ($ISrooms as $room): ?>
              <div class="room-square bg-red-300 rounded-lg shadow p-5 relative max-w-xs"
                data-room-id="<?php echo htmlspecialchars($room['roomID']); ?>"
                data-room-floor="<?php echo htmlspecialchars($room['floor']); ?>">
                <div
                  class="room-id-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 bg-gray-800 text-white text-sm rounded hidden">
                  <?php echo htmlspecialchars($room['roomID']); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div id="CS-grid" class="dep grid grid-cols-2 gap-x-4 gap-y-2">
            <?php foreach ($CSrooms as $room): ?>
              <div class="room-square bg-yellow-300 rounded-lg shadow p-5 relative max-w-xs"
                data-room-id="<?php echo htmlspecialchars($room['roomID']); ?>"
                data-room-floor="<?php echo htmlspecialchars($room['floor']); ?>">
                <div
                  class="room-id-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-2 px-2 py-1 bg-gray-800 text-white text-sm rounded hidden">
                  <?php echo htmlspecialchars($room['roomID']); ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div id="CE-grid" class="dep grid grid-cols-2 gap-x-4 gap-y-2">
            <?php foreach ($CErooms as $room): ?>
              <div class="room-square bg-blue-300 rounded-lg shadow p-5 relative max-w-xs"
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

      <!-- Departments Key -->
      <div class="hidden lg:block w-64 bg-white shadow-lg p-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Department Key</h3>
        <div class="flex items-center mb-4">
          <div class="w-4 h-4 bg-yellow-300 rounded-full mr-2"></div>
          <span class="text-sm">CS - Computer Science</span>
        </div>
        <div class="flex items-center mb-4">
          <div class="w-4 h-4 bg-blue-300 rounded-full mr-2"></div>
          <span class="text-sm">CE - Computer Engineering</span>
        </div>
        <div class="flex items-center mb-4">
          <div class="w-4 h-4 bg-red-300 rounded-full mr-2"></div>
          <span class="text-sm">IS - Information Systems</span>
        </div>
      </div>
    </div>

  </div>
  </div>
</body>