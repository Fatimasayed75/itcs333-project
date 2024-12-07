<?php
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/db-connection.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

date_default_timezone_set('Asia/Bahrain');

// Fetch current user ID from session or authorization check
$id = isAuthorized();

// Instantiate models
$roomModel = new RoomModel($pdo);
$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null); // Instantiate BookModel

$bookModel->updateExpiredBookings();

// Get the user details by ID
$userDetails = $userModel->getUserByID($id);
$isAdmin = $userDetails['role'] === 'admin';

$currentBookings = $bookModel->getCurrentBookingsByUser($id);
// Fetch booking details
$upcomingBookings = $bookModel->getUpcomingBookingsByUser($id);

// Ensure that we are safely accessing the first element in $currentBookings or $upcomingBookings
if (!empty($currentBookings)) {
  $nearestBooking = $currentBookings[0];
} elseif (!empty($upcomingBookings)) {
  $nearestBooking = $upcomingBookings[0];
} else {
  $nearestBooking = null; // No bookings available
}

// Now, safely check if $nearestBooking is set before using it
$bookingDateTime = $nearestBooking ? strtotime($nearestBooking['startTime']) : null;

$pendingBookings = $isAdmin ? $bookModel->getPendingBookings() : "";
$openLabBookings = $isAdmin ? "" : $bookModel->getOpenLabBookings($id);

// Get the last 2 open lab bookings
$lastTwoOpenLabBookings = array_slice($openLabBookings, -2); // This gives the last 2 bookings from the array
?>

<div class="w-full bg-white shadow-lg py-6 px-4 sm:px-8 lg:px-16">
  <h2 class="text-xl font-semibold text-gray-700 mb-4">Important</h2>

  <?php if (!$nearestBooking && empty($lastTwoOpenLabBookings)): ?>
    <!-- Show message when no bookings exist -->
    <p class="text-lg text-gray-700">No upcoming or open lab bookings responds available at the moment.</p>
  <?php else: ?>
    <div class="flex flex-wrap gap-4 justify-start overflow-x-auto">

      <!-- Current or nearest Coming-->
      <?php if ($nearestBooking): ?>
        <?php
        // Determine if it's a current booking or an upcoming booking
        $isCurrent = in_array($nearestBooking, $currentBookings);
        $endTime = $isCurrent ? strtotime($nearestBooking['endTime']) : $bookingDateTime;
        $timeMessage = $isCurrent ? "Ends in" : "Starts in";
        ?>
        <div class="flex-1 min-w-[250px] max-w-sm bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow p-4 relative">

          <!-- Icon for Current Booking -->
          <?php if ($isCurrent): ?>
            <i class="fa fa-clock-o text-blue-500 absolute top-2 right-2 text-2xl"></i>
          <?php endif; ?>

          <!-- Icon for Coming Booking -->
          <?php if (!$isCurrent): ?>
            <i class="fa fa-hourglass text-blue-500 absolute top-2 right-2 text-xl"></i>
          <?php endif; ?>

          <h3 class="text-lg font-medium text-gray-800"><?= $nearestBooking['roomID'] ?></h3>
          <p class="text-sm text-gray-600">Date: <?= date('d M Y', $bookingDateTime) ?></p>
          <p class="text-sm text-gray-600">Start Time: <?= date('h:i A', $bookingDateTime) ?></p>
          <p class="text-md font-semibold text-gray-700">
            <?= $timeMessage ?>:
            <span class="countdown-timer" data-end-time="<?= $endTime ?>" data-mode="<?= $isCurrent ? 'end' : 'start' ?>">
            </span>
          </p>
          <a href="#" class="text-sm text-blue-600 underline">View Details</a>
        </div>
      <?php endif; ?>

      <?php
      // Loop through the last 2 open lab bookings
      foreach ($lastTwoOpenLabBookings as $booking):
        // Calculate the time difference
        $bookingDateTime = strtotime($booking['startTime']);
        $currentDateTime = time();
        $timeDifference = $bookingDateTime - $currentDateTime;

        // Check if the booking is within the coming 2 days (172800 seconds)
        if ($timeDifference > 0 && $timeDifference <= 172800):
          // Determine card color based on status
          $cardColor = $booking['status'] === 'active' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500';
          $statusText = $booking['status'] === 'active' ? 'Request Accepted' : 'Request Rejected';
          ?>
          <!-- Reminder Card -->
          <div class="flex-1 min-w-[250px] max-w-sm <?= $cardColor ?> border-l-4 rounded-lg shadow p-4">
            <h3 class="text-lg font-medium text-gray-800"><?= htmlspecialchars($booking['roomID']) ?></h3>
            <p class="text-sm text-gray-600">Date: <?= date('d M Y', $bookingDateTime) ?></p>
            <p class="text-sm text-gray-600">Time: <?= date('h:i A', $bookingDateTime) ?></p>
            <p class="text-sm font-semibold text-gray-700"><?= $statusText ?></p>
            <a href="#" class="text-sm text-blue-600 underline">View Details</a>
          </div>
          <?php
        endif;
      endforeach;
      ?>
    </div>
  <?php endif; ?>
</div>