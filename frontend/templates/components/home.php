<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/room-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

use Utils\Constants;

$id = isAuthorized();

$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null);

$bookings = $bookModel->getAllUpcomingBookings();

$id == Constants::GUEST_USER_ID ?
  $user = ['firstName' => 'Guest', 'lastName' => 'User'] :
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
      <?php if (is_array($user)) {
        echo "Welcome, " . $user["firstName"] . " " . $user["lastName"] . "!";
      } else {
        echo "Welcome, Guest!";
      }
      ?>
    </h1>
  </div>

  <!-- Reminders Section -->
  <?php
  if ($id != Constants::GUEST_USER_ID && $id != Constants::ADMIN_USER_ID) {
    require_once 'reminders.php';
  }
  ?>

  <?php if ($id === Constants::ADMIN_USER_ID) { ?>
    <!-- Room Management Section -->
    <div class="container mx-auto mt-6">
      <h3 class="text-2xl font-semibold text-gray-800 mb-6">Room Management</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Add Room Card -->
        <div
          class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
          <div onclick="openAddRoomModal()" class="w-full cursor-pointer">
            <div class="flex flex-col items-center">
              <i class="bx bx-plus-circle text-5xl mb-4" style="color: #D885A3;"></i>
              <h4 class="text-xl font-medium text-gray-600">Add New Room</h4>
              <p class="text-sm text-gray-500 mt-2">Manage and add new rooms to the system</p>
            </div>
          </div>
        </div>
        <!-- Manage Rooms Card -->
        <div
          class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
          <div onclick="openRoomListModal()" class="w-full cursor-pointer">
            <div class="flex flex-col items-center">
              <i class="bx bx-door-open text-5xl mb-4" style="color: #D885A3;"></i>
              <h4 class="text-xl font-medium text-gray-600">Manage Rooms</h4>
              <p class="text-sm text-gray-500 mt-2">Update, edit, or remove existing rooms</p>
            </div>
          </div>
        </div>

        <div
          class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 flex flex-col justify-center items-center">
          <div onclick="openBookListModal()" class="w-full cursor-pointer">
            <div class="flex flex-col items-center">
              <i class="bx bx-calendar text-5xl mb-4" style="color: #D885A3;"></i>
              <h4 class="text-xl font-medium text-gray-600">Manage Bookings</h4>
              <p class="text-sm text-gray-500 mt-2">Update, edit, or remove upcoming Bookings</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal Container -->
    <div id="modalContainer"
      class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center overflow-y-auto py-8 max-h-screen w-full">
      <div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto">

        <!-- Add Room Modal -->
        <div id="addRoomModal" class="bg-white p-8 rounded-lg shadow-xl w-96 self-center mx-auto hidden">
          <h2 class="text-2xl font-bold mb-6 text-center">Add New Room</h2>
          <form id="addRoomForm" class="space-y-4">
            <div>
              <label for="roomID" class="block text-sm font-medium text-gray-700">Room ID</label>
              <input type="text" id="roomID" name="roomID" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
              <label for="type" class="block text-sm font-medium text-gray-700">Room Type</label>
              <select id="type" name="type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="class">Classroom</option>
                <option value="lab">Laboratory</option>
              </select>
            </div>
            <div>
              <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
              <input type="number" id="capacity" name="capacity" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <!-- <div>
                        <label for="floor" class="block text-sm font-medium text-gray-700">Floor</label>
                        <input type="number" id="floor" name="floor" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <select id="floor" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="0">Ground Floor</option>
                            <option value="1">First Floor</option>
                            <option value="2">Second Floor</option>
                        </select>
                    </div> -->
            <div class="flex justify-end space-x-4 mt-6">
              <div id="errorMessage" class="hidden text-[#D885A3] text-sm mb-4"></div>
              <button type="button" onclick="closeModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
              <button type="submit" class="px-4 py-2 text-white rounded-md" style="background-color: #D885A3;">Add
                Room</button>
            </div>
          </form>
        </div>

        <!-- Room List Modal -->
        <div id="roomListModal" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl hidden relative">
          <!-- Close button (X icon) -->
          <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
            <i class="bx bx-x"></i>
          </button>

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
                <?php foreach ($rooms as $room): ?>
                  <tr class="border-b border-gray-200 hover:bg-gray-100"
                    id="room_<?php echo htmlspecialchars($room['roomID']); ?>">
                    <td class="py-3 px-6 text-left whitespace-nowrap view-mode">
                      <?php echo htmlspecialchars($room['roomID']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($room['type']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($room['capacity']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($room['floor']); ?></td>
                    <td class="py-3 px-6 text-left view-mode">
                      <?php echo $room['isAvailable'] ? 'Available' : 'Unavailable'; ?></td>
                    <td class="py-3 px-6 text-center view-mode">
                      <div class="flex item-center justify-center space-x-4">
                        <button onclick="toggleEditMode('<?php echo htmlspecialchars($room['roomID']); ?>')"
                          class="text-blue-500 hover:text-blue-700">
                          <i class="bx bx-edit-alt"></i>
                        </button>
                        <button onclick="deleteRoom('<?php echo htmlspecialchars($room['roomID']); ?>')"
                          class="text-red-500 hover:text-red-700">
                          <i class="bx bx-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="flex justify-end mt-4">
            <button onclick="closeModal()"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Close</button>
          </div>
        </div>


        <!-- Edit Room Modal -->
        <div id="editRoomModal" class="bg-white p-8 rounded-lg shadow-xl w-96 self-center mx-auto hidden">
          <h2 class="text-2xl font-bold mb-6 text-center">Edit Room</h2>
          <form id="editRoomForm" class="space-y-4">
            <input type="hidden" id="edit_roomID" name="roomID">
            <div>
              <label for="edit_name" class="block text-sm font-medium text-gray-700">Room Name</label>
              <input type="text" id="edit_name" name="name" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
              <label for="edit_type" class="block text-sm font-medium text-gray-700">Room Type</label>
              <select id="edit_type" name="type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="class">Classroom</option>
                <option value="lab">Laboratory</option>
                <option value="meeting">Meeting Room</option>
              </select>
            </div>
            <div>
              <label for="edit_capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
              <input type="number" id="edit_capacity" name="capacity" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
              <label for="edit_floor" class="block text-sm font-medium text-gray-700">Floor</label>
              <input type="number" id="edit_floor" name="floor" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
              <label class="flex items-center">
                <input type="checkbox" id="edit_isAvailable" name="isAvailable"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">Available</span>
              </label>
            </div>
            <div class="flex justify-end space-x-4 mt-6">
              <button type="button" onclick="closeModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
              <button type="submit" class="px-4 py-2 text-white rounded-md" style="background-color: #D885A3;">Update
                Room</button>
            </div>
          </form>
        </div>


        <!-- Bookings List Modal -->
        <div id="bookListModal" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl hidden">
          <!-- Close button (X icon) -->
          <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
            <i class="bx bx-x"></i>
          </button>
          <h2 class="text-2xl font-bold mb-6 text-center">Bookings List</h2>
          <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded">
              <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                  <th class="py-3 px-6 text-left">Booking ID</th>
                  <th class="py-3 px-6 text-left">User ID</th>
                  <th class="py-3 px-6 text-left">Room ID</th>
                  <th class="py-3 px-6 text-left">Booking Time</th>
                  <th class="py-3 px-6 text-left">Start Time</th>
                  <th class="py-3 px-6 text-left">End Time</th>
                  <th class="py-3 px-6 text-left">Status</th>
                  <th class="py-3 px-6 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bookings as $booking): ?>
                  <tr class="border-b border-gray-200 hover:bg-gray-100"
                    id="book_<?php echo htmlspecialchars($booking['bookingID']); ?>">
                    <!-- Normal View -->
                    <td class="py-3 px-6 text-left whitespace-nowrap view-mode">
                      <?php echo htmlspecialchars($booking['bookingID']); ?>
                    </td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($booking['userID']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($booking['roomID']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($booking['bookingTime']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($booking['startTime']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($booking['endTime']); ?></td>
                    <td class="py-3 px-6 text-left view-mode"><?php echo htmlspecialchars($booking['status']); ?></td>
                    <td class="py-3 px-6 text-center view-mode">
                      <div class="flex item-center justify-center space-x-4">
                        <button onclick="toggleEditBooking('<?php echo htmlspecialchars($booking['bookingID']); ?>')"
                          class="text-blue-500 hover:text-blue-700">
                          <i class="bx bx-edit-alt"></i>
                        </button>
                        <button onclick="deleteBooking('<?php echo htmlspecialchars($booking['bookingID']); ?>')"
                          class="text-red-500 hover:text-red-700">
                          <i class="bx bx-trash"></i>
                        </button>
                      </div>
                    </td>

                    <!-- Edit Mode -->
                    <td class="py-3 px-6 text-left whitespace-nowrap edit-mode hidden">
                      <?php echo htmlspecialchars($booking['bookingID']); ?>
                    </td>
                    <!-- <td class="py-3 px-6 text-left edit-mode hidden">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($booking['bookingID']); ?>" 
                                           class="w-full px-2 py-1 border rounded">
                                </td> -->

                    <td class="py-3 px-6 text-left whitespace-nowrap edit-mode hidden">
                      <input type="hidden" name="userID" value="<?php echo htmlspecialchars($booking['userID']); ?>">
                    </td>

                    <td class="py-3 px-6 text-left edit-mode hidden">
                      <input type="text" name="roomID" value="<?php echo htmlspecialchars($booking['roomID']); ?>"
                        class="w-full px-2 py-1 border rounded">
                    </td>
                    <td class="py-3 px-6 text-left whitespace-nowrap edit-mode hidden">
                      <?php echo htmlspecialchars($booking['bookingTime']); ?>
                    </td>

                    <td class="py-3 px-6 text-left edit-mode hidden">
                      <input type="datetime-local" name="startTime"
                        value="<?php echo htmlspecialchars($booking['startTime']); ?>"
                        class="w-full px-2 py-1 border rounded">
                    </td>
                    <td class="py-3 px-6 text-left edit-mode hidden">
                      <input type="datetime-local" name="endTime"
                        value="<?php echo htmlspecialchars($booking['endTime']); ?>"
                        class="w-full px-2 py-1 border rounded">
                    </td>
                    <td class="py-3 px-6 text-left whitespace-nowrap edit-mode hidden">active</td>

                    <td class="py-3 px-6 text-center edit-mode hidden">
                      <div class="flex item-center justify-center space-x-4">
                        <button onclick="saveBooking('<?php echo htmlspecialchars($booking['bookingID']); ?>')"
                          class="text-green-500 hover:text-green-700">
                          <i class="bx bx-check"></i>
                        </button>
                        <button onclick="cancelBookingEdit('<?php echo htmlspecialchars($booking['bookingID']); ?>')"
                          class="text-gray-500 hover:text-gray-700">
                          <i class="bx bx-x"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="flex justify-end mt-4">
            <button onclick="closeModal()"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Close</button>
          </div>
        </div>



      </div>
    </div>
  <?php } ?>

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
          <!-- Filter Icon -->
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
    <div id="roomsContainer"
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 justify-center items-center">
      <!-- No Results Message (Initially Hidden) -->
      <p id="noResultsMessage" class="text-center p-4 text-red-500 font-semibold hidden col-span-full">
        No rooms found based on your filter/search criteria.
      </p>

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
              <i class="fa fa-minus-circle text-red-500 text-xl" title="Not Available"></i>
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

</html>