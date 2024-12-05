<?php
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/db-connection.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

use Utils\Constants;

date_default_timezone_set('Asia/Bahrain');

// Fetch current user ID from session or authorization check
$id = isAuthorized();

// Instantiate models
$commentModel = new CommentModel($pdo);
$roomModel = new RoomModel($pdo);
$commentReplyModel = new CommentReplyModel($pdo);
$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null); // Instantiate BookModel

$bookModel->updateExpiredBookings();

// Fetch all comments made by users
$comments = $commentModel->getAllComments();

// Get the user details by ID
$userDetails = $userModel->getUserByID($id);
$isAdmin = $userDetails['role'] === 'admin';

// Fetch booking details
$bookingDetails = $isAdmin 
    ? $bookModel->getAllBookings() // Admins see all bookings
    : $bookModel->getPreviousBookingsByUser($id); // Normal users see their bookings only

$pendingBookings = $isAdmin ? $bookModel->getPendingBookings() : "";
$openLabBookings = $isAdmin ? "" : $bookModel->getOpenLabBookings($id);


// Prepare an associative array for comments with replies and booking details
$commentsWithReplies = [];

// Loop through each comment to fetch replies and link to bookings
foreach ($comments as $comment) {
    $commentID = $comment['commentID'];
    $roomID = $comment['roomID'];
    $bookingID = $comment['bookingID']; // Booking ID linked to the comment
    $commentContent = $comment['content'];
    $createdAt = $comment['createdAt'];

    // Fetch replies for this comment
    $replies = $commentReplyModel->getRepliesByCommentID($commentID);
    $latestReplyDate = $createdAt;

    if (!empty($replies)) {
        // Sort replies by the created date in descending order, most recent first
        usort($replies, function ($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        // Get the most recent reply date
        $latestReplyDate = $replies[0]['createdAt'];

        usort($replies, function ($a, $b) {
            return strtotime($a['createdAt']) - strtotime($b['createdAt']);
        });
    }

    // Fetch the corresponding booking details using bookingID
    $booking = array_filter($bookingDetails, function ($booking) use ($bookingID) {
        return $booking['bookingID'] == $bookingID;
    });

    // If a matching booking exists, get the formatted details
    $formattedDetails = [];
    if (!empty($booking)) {
        $booking = array_shift($booking); // Get the first matching booking
        $formattedDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
    }

    // Add comment, replies, and booking details to the array
    $commentsWithReplies[] = [
        'comment' => $comment,
        'latestReplyDate' => $latestReplyDate,
        'replies' => $replies,
        'bookingDetails' => $formattedDetails
    ];
}

// Sort the comments by the latest reply date (newest first)
usort($commentsWithReplies, function ($a, $b) {
    return strtotime($b['latestReplyDate']) - strtotime($a['latestReplyDate']);
});

?>

<div class="notifications p-6 space-y-6 bg-gray-100 rounded-lg pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10 min-h-screen">

    <!-- Pending Bookings Section -->
    <?php if($isAdmin && !empty($pendingBookings)) { ?>
        <h1 class="text-3xl font-semibold text-gray-800 mb-4">Pending Bookings</h1>
        <div class="pending-bookings max-w-4xl mx-auto p-6 space-y-6 bg-gray-100 rounded-lg pb-6 mt-10 w-full">
        <!-- <div class="pending-booking bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow relative mb-6" > -->

            <?php foreach ($pendingBookings as $booking):
                $bookingID = $booking['bookingID'];
                $userID = $booking['userID'];
                $roomID = $booking['roomID'];
                $startTime = $booking['startTime'];
                $endTime = $booking['endTime'];

                $userDetails = $userModel->getUserByID($userID);
                
            ?>

    <div class="pending-booking bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow relative" id="pending-booking-<?= $bookingID; ?>">
        <div class="booking-header flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <i class="fa fa-calendar text-2xl text-orange-600" style="color: #D885A3;"></i>
                <div>
                    <p class="text-sm text-gray-500">
                        <strong>User:</strong> <?= $userDetails['firstName'] . ' ' . $userDetails['lastName']; ?> | <strong>Room:</strong> <?= $roomID ?>
                    </p>
                    <p class="text-sm text-gray-500">
                        <strong>Date:</strong> <?= date("M d, Y", strtotime($startTime)); ?> | 
                        <strong>Time:</strong> <?= date("h:i A", strtotime($startTime)); ?> - <?= date("h:i A", strtotime($endTime)); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="booking-actions flex justify-center mt-4 space-x-4">
            <button class="approve-booking px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" style="background-color: #D885A3;" data-booking-id="<?= $bookingID; ?>" data-status="approved">
                Approve
            </button>
            <button class="reject-booking px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-600" style="background-color: #B0B0B0;" data-booking-id="<?= $bookingID; ?>" data-status="rejected">
                Reject
            </button>
        </div>


        <!-- Hidden fields for AJAX request -->
        <input type="hidden" id="roomID-<?= $bookingID; ?>" value="<?= $roomID; ?>" />
        <input type="hidden" id="bookingTime-<?= $bookingID; ?>" value="<?= $bookingTime; ?>" />
        <input type="hidden" id="startTime-<?= $bookingID; ?>" value="<?= $startTime; ?>" />
        <input type="hidden" id="endTime-<?= $bookingID; ?>" value="<?= $endTime; ?>" />
        <input type="hidden" id="userID-<?= $bookingID; ?>" value="<?= $userID; ?>" />
    </div>

            <?php endforeach; ?>
    </div>

    <?php } ?>

    <!-- Modal for Success/Failure -->
<div id="status-modal" class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-1/3 max-w-sm">
        <div class="flex justify-between items-center">
            <h3 id="modal-title" class="text-xl font-semibold">Booking Status</h3>
            <button id="close-modal" class="text-gray-500 hover:text-gray-800">&times;</button>
        </div>
        <p id="modal-message" class="mt-4 text-gray-700"></p>
        <div class="mt-4 flex justify-end">
            <button id="close-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md">Close</button>
        </div>
    </div>
</div>


    <h1 class="text-3xl font-semibold text-gray-800 mb-4">Notifications</h1>

    <?php if (!empty($openLabBookings)): ?>
    <?php foreach ($openLabBookings as $openLabBooking): ?>
        <div class="notification bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow relative" 
            id="notification-<?= $openLabBooking['bookingID']; ?>">

            <!-- Notification Header -->
            <div class="notification-header flex items-center justify-between">
            <!-- <div class="important-notification-label absolute top-3 right-10 bg-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #D885A3;">
                    Important
                </div> -->
                <div class="flex items-center space-x-4">
                <?php if ($openLabBooking['status'] === 'active'): ?>
                    <i class="fa fa-bell text-2xl text-red-700" style="color: #D885A3;"></i>
                <?php elseif ($openLabBooking['status'] === 'rejected'): ?>
                    <i class="fa fa-bell text-2xl text-green-700" style="color: #D885A3;"></i>
                <?php endif; ?>
                    <div>
                        <p class="text-sm text-gray-500">
                            <strong>Room:</strong> <?= $openLabBooking['roomID']; ?> | 
                            <strong>Date:</strong> <?= date("M d, Y", strtotime($openLabBooking['startTime'])); ?>
                        </p>
                        <p class="text-sm text-gray-500">
                            <strong>Time:</strong> <?= date("h:i A", strtotime($openLabBooking['startTime'])); ?> - 
                            <?= date("h:i A", strtotime($openLabBooking['endTime'])); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notification Body -->
            <div class="notification-details mt-4">
                <p class="text-base text-gray-700" style="color: #D885A3;">
                    <?php if ($openLabBooking['status'] === 'active'): ?>
                        Your booking has been approved!
                    <?php elseif ($openLabBooking['status'] === 'rejected'): ?>
                        Unfortunately, your booking has been rejected.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Close Button -->
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" onclick="this.parentElement.style.display='none';">
                <i class="fa fa-times"></i>
            </button>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>




    <?php foreach ($commentsWithReplies as $commentData): 
        $comment = $commentData['comment'];
        $commentID = $comment['commentID'];
        $roomID = $comment['roomID'];
        $commentContent = $comment['content'];
        $createdAt = $comment['createdAt'];
        $replies = $commentData['replies'];
        $formattedDetails = $commentData['bookingDetails'];

        $bookingDate = $formattedDetails['date'] ?? '';
        $startTime = $formattedDetails['startTime'] ?? '';
        $endTime = $formattedDetails['endTime'] ?? '';

        $lastReplyIsAdmin = false;

        // Check if the comment has a reply from the admin
        if ($isAdmin || $commentModel->hasAdminReply($commentID)): ?>
            <div class="comment bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow relative" id="comment-<?= $commentID; ?>">
                <!-- Notification Header -->
                <div class="notification-header flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <i class="fa fa-bell text-2xl" style="color: #D885A3;"></i>
                        <div>
                            <p class="text-sm text-gray-500">
                                <strong>Room:</strong> <?= $roomID; ?> | <strong>Date:</strong> <?= $bookingDate; ?>
                            </p>
                            <p class="text-sm text-gray-500">
                                <strong>Time:</strong> <?= $startTime; ?> - <?= $endTime; ?>
                            </p>
                        </div>
                    </div>
                    <i class="expand-icon fa fa-chevron-down text-gray-400 cursor-pointer" onclick="toggleDetails(<?= $commentID; ?>, this)"></i>
                </div>

                <!-- Notification Details -->
                <div class="notification-details mt-4 hidden" id="details-<?= $commentID; ?>">
                    <div class="reply bg-gray-50 border-l-4 border-gray-300 p-4 shadow-sm">
                        <p class="text-sm text-gray-800">
                            <strong>Feedback:</strong> <?= $commentContent; ?>
                        </p>
                    </div>

                    <?php if (!empty($replies)): ?>
                        <div class="replies mt-4 space-y-4" id="replies-<?= $commentID; ?>">
                        <?php foreach ($replies as $index => $reply):
                            $isAdminReply = ($reply['userID'] == Constants::ADMIN_USER_ID);
                            if ($isAdminReply && $index === count($replies) - 1) {
                                $lastReplyIsAdmin = true;
                            }

                            $userFullName = $isAdminReply ? 'Admin' : $commentModel->getUserFullName( $reply['userID']);
                            $replyClass = $isAdminReply ? 'bg-green-50 border-l-4 border-green-400' : 'bg-gray-50 border-l-4 border-gray-300';
                            ?>
                            <div class="reply p-3 <?= $replyClass; ?> shadow-sm">
                                <p class="text-sm text-gray-800">
                                    <strong><?= $userFullName; ?>:</strong>
                                    <?= htmlspecialchars($reply['replyContent']); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Posted on: <?= date("M d, Y h:i A", strtotime($reply['createdAt'])); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php
                        $fullName = $isAdmin ? 'Admin' : $commentModel->getUserFullName($id);
                    ?>

                    <?php if ($lastReplyIsAdmin && $isAdmin === false): ?>
                        <div class="reply-section mt-6 space-y-4" id="reply-section-<?= $commentID; ?>">
                            <textarea id="replyContent-<?= $commentID; ?>" required class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write your reply here..."></textarea>
                            <div class="flex justify-center">
                            <button class="reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" 
                                data-comment-id="<?= $commentID; ?>" >
                                Reply
                            </button>
                            </div>
                        </div>
                    <?php elseif ($isAdmin): ?>
                        <div class="admin-reply-section mt-6 space-y-4" id="admin-reply-section-<?= $commentID; ?>">
                            <textarea id="adminReplyContent-<?= $commentID; ?>" required class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write your reply here..."></textarea>
                            <div class="flex justify-center">
                                <button class="admin-reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" 
                                data-comment-id="<?= $commentID; ?>">
                                Reply
                            </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
