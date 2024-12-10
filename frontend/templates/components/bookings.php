<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/utils/helpers.php';

date_default_timezone_set('Asia/Bahrain');

$id = isAuthorized();

$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, $id, null, null, null, null, null);

$user = $userModel->getUserByID($id);
$upcomingBookings = $bookModel->getUpcomingBookingsByUser($id);
$currentBookings = $bookModel->getCurrentBookingsByUser($id);
$previousBookings = $bookModel->getPreviousBookingsByUser($id);

$bookModel->updateExpiredBookings();

?>

<body class="bg-gray-50 min-h-screen px-6 py-12">
    <div class="pt-10">

        <!-- Section: Upcoming Bookings -->
        <div class="w-full bg-white shadow-lg rounded-xl my-6 py-6 px-6 sm:px-8 upcoming-bookings-section">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Upcoming Bookings</h2>
            <?php if (!empty($upcomingBookings)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 justify-items-center">
                    <?php foreach ($upcomingBookings as $booking): ?>
                        <?php
                        // Get formatted details
                        $bookingDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                        ?>
                        <div id="booking-card-<?php echo $booking['bookingID']; ?>"
                            class="bg-blue-50 rounded-lg shadow p-6 relative w-full">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Room: <?php echo $booking['roomID']; ?></h3>
                            <p class="text-sm text-gray-700 mb-1">Date: <?php echo $bookingDetails['date']; ?></p>
                            <p class="text-sm text-gray-700 mb-1">Day: <?php echo $bookingDetails['day']; ?></p>
                            <p class="text-sm text-gray-700 mb-1">Start: <?php echo $bookingDetails['startTime']; ?></p>
                            <p class="text-sm text-gray-700 mb-1">End: <?php echo $bookingDetails['endTime']; ?></p>
                            <p class="text-sm text-gray-700">Duration: <?php echo $bookingDetails['duration']; ?></p>

                            <!-- Cancel Booking Button -->
                            <div class="flex justify-center">
                                <button type="button"
                                    class="cancel-booking-btn bg-blue-400 text-white py-2 px-3 text-xs rounded transition duration-200 hover:bg-blue-500 mt-4"
                                    onclick="showConfirmation('<?php echo $booking['bookingID']; ?>')">
                                    Cancel Booking
                                </button>
                            </div>

                            <!-- Confirmation Box -->
                            <div id="confirm-box-<?php echo $booking['bookingID']; ?>"
                                class="hidden fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-20">
                                <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-sm text-center">
                                    <h4 class="text-xl font-semibold text-gray-800 mb-4">Confirm Cancellation</h4>
                                    <p class="text-sm mb-6">Are you sure you want to cancel this booking?</p>
                                    <div class="flex justify-center gap-6">
                                        <button type="button"
                                            class="bg-gray-300 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-400"
                                            onclick="hideConfirmation('<?php echo $booking['bookingID']; ?>')">
                                            No
                                        </button>
                                        <button type="button"
                                            class="bg-blue-400 text-white py-2 px-6 rounded-lg hover:bg-blue-500"
                                            onclick="cancelBooking('<?php echo $booking['bookingID']; ?>')">
                                            Yes, Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No upcoming bookings.</p>
            <?php endif; ?>
        </div>

        <!-- Section: Current Bookings -->
        <div class="w-full bg-white shadow-lg rounded-xl my-6 py-6 px-6 sm:px-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Current Bookings</h2>
            <?php if (!empty($currentBookings)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 justify-items-center">
                    <?php foreach ($currentBookings as $booking): ?>
                        <?php
                        // Get formatted details
                        $bookingDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                        ?>
                        <div class="bg-green-50 rounded-lg shadow p-6 relative w-full">
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
                <p>No current bookings.</p>
            <?php endif; ?>
        </div>

        <!-- Previous Bookings Section -->
        <div class="w-full bg-white shadow-lg rounded-xl my-6 py-6 px-6 sm:px-8 previous-bookings-section">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Previous Bookings</h2>
            <?php if (!empty($previousBookings)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 justify-items-center">
                    <?php foreach ($previousBookings as $booking): ?>
                        <?php
                        // Get formatted details for the booking
                        $bookingDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                        ?>
                        <div id="previous-booking-card-<?php echo $booking['bookingID']; ?>"
                            class="bg-gray-100 rounded-lg shadow p-6 relative w-full previous-booking-card"
                            data-booking-id="<?php echo $booking['bookingID']; ?>">
                            <h3 class="text-lg font-medium mb-2">Room: <?php echo $booking['roomID']; ?></h3>
                            <p class="text-sm mb-1">Date: <?php echo $bookingDetails['date']; ?></p>
                            <p class="text-sm mb-1">Day: <?php echo $bookingDetails['day']; ?></p>
                            <p class="text-sm mb-1">Start: <?php echo $bookingDetails['startTime']; ?></p>
                            <p class="text-sm mb-1">End: <?php echo $bookingDetails['endTime']; ?></p>
                            <p class="text-sm mb-2">Duration: <?php echo $bookingDetails['duration']; ?></p>

                            <!-- Buttons or Feedback Indicator -->
                            <div class="flex justify-center gap-4 mt-4 items-center">
                                <!-- Rebook Button -->
                                <button
                                    class="rebook-btn bg-gray-400 text-white py-2 px-3 text-xs rounded transition duration-200 hover:bg-gray-500"
                                    data-room-id="<?php echo $booking['roomID']; ?>"
                                    onclick="handleRebook('<?php echo $booking['bookingID']; ?>')">
                                    Rebook
                                </button>

                                <!-- Feedback Button or Feedback Status -->
                                <?php if ($booking['feedback'] == 0): ?>
                                    <button id="feedback-btn-<?php echo $booking['bookingID']; ?>"
                                        class="add-comment-btn bg-gray-500 text-white py-2 px-3 text-xs rounded transition duration-200 hover:bg-gray-600"
                                        onclick="showModal('<?php echo $booking['bookingID']; ?>', '<?php echo $booking['roomID']; ?>')">
                                        Feedback
                                    </button>
                                <?php else: ?>
                                    <!-- Feedback Submitted Status -->
                                    <span id="feedback-status-<?php echo $booking['bookingID']; ?>"
                                        class="text-gray-600 text-xs font-semibold flex items-center justify-center">
                                        ✔ Feedback Submitted
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No previous bookings found.</p>
            <?php endif; ?>
        </div>

        <!-- Modal for adding comment -->
        <div id="comment-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
                <h3 class="text-lg font-semibold mb-4">Feedback</h3>
                <textarea class="w-full border rounded-lg p-2 text-sm mb-4" rows="4"
                    placeholder="Enter your feedback here..." id="comment-input-modal"></textarea>

                <!-- Error message display -->
                <div id="error-message" class="text-red-600 text-sm mb-4 hidden">
                    Comment cannot be empty!
                </div>

                <div class="flex justify-end gap-2">
                    <button class="cancel-comment-btn bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400"
                        onclick="hideModal()">
                        Cancel
                    </button>
                    <button class="save-comment-btn bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600"
                        onclick="saveComment()">
                        Save
                    </button>
                </div>
            </div>
        </div>


    </div>
</body>