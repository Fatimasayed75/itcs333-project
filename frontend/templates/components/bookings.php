<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/utils/helpers.php';

$id = isAuthorized();

$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null);

$user = $userModel->getUserByID($id);
$upcomingBookings = $bookModel->getUpcomingBookingsByUser($id);
$currentBookings = $bookModel->getCurrentBookingsByUser($id);
$previousBookings = $bookModel->getPreviousBookingsByUser($id);
?>

<body class="bg-gray-100 min-h-screen px-5 py-10">
  <!-- Section: Upcoming Bookings -->
  <div class="w-full bg-white shadow-lg my-6 py-6 px-4 sm:px-8 upcoming-bookings-section">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Upcoming Bookings</h2>
    <?php if (!empty($upcomingBookings)): ?>
        <div class="flex flex-wrap gap-4">
            <?php foreach ($upcomingBookings as $booking): ?>
                <div id="booking-card-<?php echo $booking['bookingID']; ?>" class="w-full sm:w-1/2 lg:w-1/3 bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow p-4 relative">
                    <h3 class="text-lg font-medium text-gray-800">Room: <?php echo $booking['roomID']; ?></h3>
                    <p class="text-sm text-gray-600">Start: <?php echo $booking['startTime']; ?></p>
                    <p class="text-sm text-gray-600">End: <?php echo $booking['endTime']; ?></p>

                    <!-- Cancel Booking Button -->
                    <button 
                        type="button" 
                        class="cancel-booking-btn bg-red-500 text-white py-1 px-4 rounded mt-2"
                        onclick="showConfirmation('<?php echo $booking['bookingID']; ?>')"
                    >
                        Cancel Booking
                    </button>

                    <!-- Confirmation Box -->
                    <div id="confirm-box-<?php echo $booking['bookingID']; ?>" 
                         class="hidden fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-10">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm text-center">
                            <h4 class="text-xl font-semibold text-gray-800 mb-4">Confirm Cancellation</h4>
                            <p class="text-sm text-gray-600 mb-6">Are you sure you want to cancel this booking?</p>
                            <div class="flex justify-center gap-4">
                                <button 
                                    type="button" 
                                    class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
                                    onclick="hideConfirmation('<?php echo $booking['bookingID']; ?>')"
                                >
                                    No
                                </button>
                                <button 
                                    type="button" 
                                    class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600"
                                    onclick="cancelBooking('<?php echo $booking['bookingID']; ?>')"
                                >
                                    Yes, Cancel
                                </button>
                            </div>
                        </div>
                    </div>
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
</body>
