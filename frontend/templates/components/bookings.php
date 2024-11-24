<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/utils/helpers.php';

$id = isAuthorized();

// Initialize Models
$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null);

$user = $userModel->getUserByID($id);
$upcomingBookings = $bookModel->getUpcomingBookingsByUser($id);
$currentBookings = $bookModel->getCurrentBookingsByUser($id);
$previousBookings = $bookModel->getPreviousBookingsByUser($id);
?>

<body class="bg-gray-100 min-h-screen px-5 py-10">
 
  <!-- Section: Upcoming Bookings -->
  <div class="w-full bg-white shadow-lg my-6 py-6 px-4 sm:px-8">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Upcoming Bookings</h2>
    <?php if (!empty($upcomingBookings)): ?>
      <div class="flex flex-wrap gap-4">
        <?php foreach ($upcomingBookings as $booking): ?>
          <div class="w-full sm:w-1/2 lg:w-1/3 bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow p-4">
            <h3 class="text-lg font-medium text-gray-800">Room: <?php echo $booking['roomID']; ?></h3>
            <p class="text-sm text-gray-600">Start: <?php echo $booking['startTime']; ?></p>
            <p class="text-sm text-gray-600">End: <?php echo $booking['endTime']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">No upcoming bookings.</p>
    <?php endif; ?>
  </div>

  <!-- Section: Current Bookings -->
  <div class="w-full bg-white shadow-lg my-6 py-6 px-4 sm:px-8">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Current Bookings</h2>
    <?php if (!empty($currentBookings)): ?>
      <div class="flex flex-wrap gap-4">
        <?php foreach ($currentBookings as $booking): ?>
          <div class="w-full sm:w-1/2 lg:w-1/3 bg-green-50 border-l-4 border-green-500 rounded-lg shadow p-4">
            <h3 class="text-lg font-medium text-gray-800">Room: <?php echo $booking['roomID']; ?></h3>
            <p class="text-sm text-gray-600">Start: <?php echo $booking['startTime']; ?></p>
            <p class="text-sm text-gray-600">End: <?php echo $booking['endTime']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">No current bookings.</p>
    <?php endif; ?>
  </div>

  <!-- Section: Previous Bookings -->
  <div class="w-full bg-white shadow-lg my-6 py-6 px-4 sm:px-8">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Previous Bookings</h2>
    <?php if (!empty($previousBookings)): ?>
      <div class="flex flex-wrap gap-4">
        <?php foreach ($previousBookings as $booking): ?>
          <div class="w-full sm:w-1/2 lg:w-1/3 bg-gray-50 border-l-4 border-gray-500 rounded-lg shadow p-4">
            <h3 class="text-lg font-medium text-gray-800">Room: <?php echo $booking['roomID']; ?></h3>
            <p class="text-sm text-gray-600">Start: <?php echo $booking['startTime']; ?></p>
            <p class="text-sm text-gray-600">End: <?php echo $booking['endTime']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-gray-500">No previous bookings.</p>
    <?php endif; ?>
  </div>

  <!-- Modal Structure -->
  <div id="bookingModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h3>Booking Details</h3>
        <p><strong>Booking ID:</strong> <span id="bookingID"></span></p>
        <p><strong>User ID:</strong> <span id="userID"></span></p>
        <p><strong>Start Time:</strong> <span id="startTime"></span></p>
        <p><strong>End Time:</strong> <span id="endTime"></span></p>
        <p><strong>Status:</strong> <span id="status"></span></p>
    </div>
  </div>

</body>
