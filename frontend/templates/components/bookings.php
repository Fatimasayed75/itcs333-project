<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/utils/helpers.php';

date_default_timezone_set('Asia/Bahrain');

$id = isAuthorized();

$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null);

$user = $userModel->getUserByID($id);
$upcomingBookings = $bookModel->getUpcomingBookingsByUser($id);
$currentBookings = $bookModel->getCurrentBookingsByUser($id);
$previousBookings = $bookModel->getPreviousBookingsByUser($id);

// Function to format time and calculate duration
function formatBookingDetails($startTime, $endTime) {
    // Convert to DateTime objects
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);

    // Date and Day format
    $date = $start->format('M d, Y');
    $day = $start->format('l');

    // Time format
    $startTimeFormatted = $start->format('g:i A');
    $endTimeFormatted = $end->format('g:i A');    

    // Duration calculation
    $duration = $start->diff($end);
    if ($duration->h > 0) {
        $durationFormatted = $duration->h . 'h and ' . $duration->i . ' min';
    } else {
        $durationFormatted = $duration->i . ' min';
    }

    return [
        'date' => $date,
        'day' => $day,
        'startTime' => $startTimeFormatted,
        'endTime' => $endTimeFormatted,
        'duration' => $durationFormatted
    ];
}
?>

<body class="bg-gray-50 min-h-screen px-6 py-12">
  <div class="pt-4">
  
  <!-- Section: Upcoming Bookings -->
  <div class="w-full bg-white shadow-lg rounded-xl my-6 py-6 px-6 sm:px-8 upcoming-bookings-section">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Upcoming Bookings</h2>
    <?php if (!empty($upcomingBookings)): ?>
        <div class="flex flex-wrap gap-4">
            <?php foreach ($upcomingBookings as $booking): ?>
                <?php 
                    // Get formatted details
                    $bookingDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                ?>
                <div id="booking-card-<?php echo $booking['bookingID']; ?>" class="w-full sm:w-1/3 lg:w-1/4 bg-blue-50 rounded-lg shadow p-4 relative">
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Room: <?php echo $booking['roomID']; ?></h3>
                    <p class="text-sm text-gray-700 mb-1">Date: <?php echo $bookingDetails['date']; ?></p>
                    <p class="text-sm text-gray-700 mb-1">Day: <?php echo $bookingDetails['day']; ?></p>
                    <p class="text-sm text-gray-700 mb-1">Start: <?php echo $bookingDetails['startTime']; ?></p>
                    <p class="text-sm text-gray-700 mb-1">End: <?php echo $bookingDetails['endTime']; ?></p>
                    <p class="text-sm text-gray-700">Duration: <?php echo $bookingDetails['duration']; ?></p>

                    <!-- Cancel Booking Button -->
                    <div class="flex justify-center">
                        <button 
                            type="button" 
                            class="cancel-booking-btn bg-blue-500 text-white py-2 px-3 text-xs rounded transition duration-200 hover:bg-red-600 mt-4"
                            onclick="showConfirmation('<?php echo $booking['bookingID']; ?>')"
                        >
                            Cancel Booking
                        </button>
                    </div>

                    <!-- Confirmation Box -->
                    <div id="confirm-box-<?php echo $booking['bookingID']; ?>" 
                         class="hidden fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-20">
                        <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm text-center">
                            <h4 class="text-xl font-semibold text-gray-800 mb-4">Confirm Cancellation</h4>
                            <p class="text-sm text-gray-600 mb-6">Are you sure you want to cancel this booking?</p>
                            <div class="flex justify-center gap-6">
                                <button 
                                    type="button" 
                                    class="bg-gray-300 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-400"
                                    onclick="hideConfirmation('<?php echo $booking['bookingID']; ?>')"
                                >
                                    No
                                </button>
                                <button 
                                    type="button" 
                                    class="bg-red-500 text-white py-2 px-6 rounded-lg hover:bg-red-600"
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
  <div class="w-full bg-white shadow-lg rounded-xl my-6 py-6 px-6 sm:px-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Current Bookings</h2>
    <?php if (!empty($currentBookings)): ?>
        <div class="flex flex-wrap gap-4">
            <?php foreach ($currentBookings as $booking): ?>
                <?php 
                    // Get formatted details
                    $bookingDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                ?>
                <div class="w-full sm:w-1/3 lg:w-1/4 bg-green-50 rounded-lg shadow p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Room: <?php echo $booking['roomID']; ?></h3>
                    <p class="text-sm text-gray-700 mb-1">Date: <?php echo $bookingDetails['date']; ?></p>
                    <p class="text-sm text-gray-700 mb-1">Day: <?php echo $bookingDetails['day']; ?></p>
                    <p class="text-sm text-gray-700 mb-1">Start: <?php echo $bookingDetails['startTime']; ?></p>
                    <p class="text-sm text-gray-700 mb-1">End: <?php echo $bookingDetails['endTime']; ?></p>
                    <p class="text-sm text-gray-700">Duration: <?php echo $bookingDetails['duration']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">No current bookings.</p>
    <?php endif; ?>
  </div>

<!-- Previous Bookings Section -->
<div class="w-full bg-white shadow-lg rounded-xl my-6 py-6 px-6 sm:px-8 previous-bookings-section">
  <h2 class="text-2xl font-semibold text-gray-800 mb-6">Previous Bookings</h2>
  <?php if (!empty($previousBookings)): ?>
    <div class="flex flex-wrap gap-4">
        <?php foreach ($previousBookings as $booking): ?>
            <?php 
                // Get formatted details for the booking
                $bookingDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
            ?>
            <div id="previous-booking-card-<?php echo $booking['bookingID']; ?>" class="w-full sm:w-1/3 lg:w-1/4 bg-orange-50 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Room: <?php echo $booking['roomID']; ?></h3>
                <p class="text-sm text-gray-700 mb-1">Date: <?php echo $bookingDetails['date']; ?></p>
                <p class="text-sm text-gray-700 mb-1">Day: <?php echo $bookingDetails['day']; ?></p>
                <p class="text-sm text-gray-700 mb-1">Start: <?php echo $bookingDetails['startTime']; ?></p>
                <p class="text-sm text-gray-700 mb-1">End: <?php echo $bookingDetails['endTime']; ?></p>
                <p class="text-sm text-gray-700 mb-2">Duration: <?php echo $bookingDetails['duration']; ?></p>

                <!-- Rebook Button -->
                <div class="flex justify-center">
                    <button 
                        class="rebook-btn bg-orange-500 text-white py-2 px-3 text-xs rounded transition duration-200 hover:bg-teal-600 mt-4"
                        data-room-id="<?php echo $booking['roomID']; ?>"
                    >
                        Rebook
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-700">No previous bookings found.</p>
  <?php endif; ?>
</div>
  
  </div>
</body>
