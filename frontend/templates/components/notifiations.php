<?php
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/db-connection.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

USE Utils\Constants;

date_default_timezone_set('Asia/Bahrain');

// Fetch current user ID from session or authorization check
$id = isAuthorized();

// Instantiate models
$commentModel = new CommentModel($pdo);
$commentReplyModel = new CommentReplyModel($pdo);
$userModel = new UserModel($pdo);
$bookModel = new BookModel($pdo, null, null, null, null, null, null); // Instantiate BookModel

// Fetch all comments made by users
$comments = $commentModel->getAllComments();

// Get the user details by ID
$userDetails = $userModel->getUserByID($id);
$isAdmin = $userDetails['role'] === 'admin';

// Fetch booking details
$bookingDetails = $isAdmin 
    ? $bookModel->getAllBookings() // Admins see all bookings
    : $bookModel->getPreviousBookingsByUser($id); // Normal users see their bookings only

// Prepare an associative array to map roomID to its booking details
$bookingDetailsByRoom = [];
foreach ($bookingDetails as $booking) {
    $bookingDetailsByRoom[$booking['roomID']][] = $booking;  // Multiple bookings per room
}

// Prepare an array for comments with the latest reply dates
$commentsWithReplies = [];

// Loop through each comment to fetch its replies and sort by the latest reply
foreach ($comments as $comment) {
    $commentID = $comment['commentID'];
    $roomID = $comment['roomID'];
    $commentContent = $comment['content'];
    $createdAt = $comment['createdAt'];

    // Get replies for this comment
    $replies = $commentReplyModel->getRepliesByCommentID($commentID);
    $latestReplyDate = $createdAt; // Default to the comment creation date

    if (!empty($replies)) {
        // Sort replies by the created date in descending order, most recent first
        usort($replies, function($a, $b) {
            return strtotime($a['createdAt']) - strtotime($b['createdAt']);
        });


        // Get the most recent reply date
        $latestReplyDate = $replies[0]['createdAt'];
    }

    // Store the comment and its latest reply date for sorting
    $commentsWithReplies[] = [
        'comment' => $comment,
        'latestReplyDate' => $latestReplyDate,
        'replies' => $replies
    ];
}

// Sort the comments by the latest reply date (newest first)
usort($commentsWithReplies, function($a, $b) {
    return strtotime($b['latestReplyDate']) - strtotime($a['latestReplyDate']);
});

?>

<div class="notifications p-6 space-y-6 bg-gray-100 rounded-lg pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800">Notifications</h1>

    <?php
    // Loop through the sorted comments
    foreach ($commentsWithReplies as $commentData) {
        $comment = $commentData['comment'];
        $commentID = $comment['commentID'];
        $roomID = $comment['roomID'];
        $commentContent = $comment['content'];
        $createdAt = $comment['createdAt'];
        $replies = $commentData['replies'];

        // Initialize bookingDate variable to avoid the undefined error
        $bookingDate = '';

        // Initialize $lastReplyIsAdmin to false by default
        $lastReplyIsAdmin = false;

        // Check if the comment has a reply from the admin
        if ($isAdmin || $commentModel->hasAdminReply($commentID)) {
            echo "<div class='comment p-4 bg-white rounded-lg shadow-md space-y-4' id='comment-{$commentID}'>";

            // Get booking details for this room
            $bookingsForRoom = isset($bookingDetailsByRoom[$roomID]) ? $bookingDetailsByRoom[$roomID] : null;
            if ($bookingsForRoom) {
                // Format booking details for the first booking
                $booking = $bookingsForRoom[0]; // Take the first booking for this room
                $formattedDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                $bookingDate = $formattedDetails['date']; // Assign the booking date
            }

            // Notification header
            echo "<div class='notification-header flex justify-between items-center' data-comment-id='{$commentID}'>";
            echo "<p class='text-lg font-medium text-gray-700'><strong>Room:</strong> {$roomID} | <strong>Date:</strong> {$bookingDate}</p>";
            echo "<span class='text-sm text-gray-500'> {$formattedDetails['startTime']} to {$formattedDetails['endTime']}</span>";
            // Add the clickable icon with an onclick event
            echo "<i class='expand-icon fa fa-chevron-down ml-2 cursor-pointer' onclick='toggleDetails({$commentID}, this)'></i>";
            echo "</div>";

            // Hidden details initially
            echo "<div class='notification-details mt-4 space-y-4 hidden' id='details-{$commentID}'>";

            // Display booking details only if bookings exist for this room
            if ($bookingsForRoom) {
                foreach ($bookingsForRoom as $booking) {
                    // Format booking details
                    $formattedDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                    
                    // Display formatted booking details
                    echo "<p class='text-gray-700'><strong>Day:</strong> {$formattedDetails['day']}</p>";
                    echo "<p class='text-gray-700'><strong>Duration:</strong> {$formattedDetails['duration']}</p>";
                }
            }

            // Display comment content
            echo "<div class='reply p-4 bg-gray-50 border-l-4 border-gray-300 shadow-sm'>";
            echo "<p class='font-medium text-gray-800'><strong>User Feedback:</strong> {$commentContent}</p>";
            echo "</div>";

            // Display replies for this comment
            echo "<div class='replies mt-4 space-y-4' id='replies-{$commentID}'>";

            if (!empty($replies)) {
                foreach ($replies as $index => $reply) {
                    // Check if the reply is from the admin
                    $isAdminReply = ($reply['userID'] == Constants::ADMIN_USER_ID);
                    if ($isAdminReply && $index === count($replies) - 1) {
                        $lastReplyIsAdmin = true; // Set the flag if the last reply is from the admin
                    }

                    $replyClass = $isAdminReply ? 'bg-green-50 border-l-4 border-green-400' : 'bg-gray-50 border-l-4 border-gray-300';
                    echo "<div class='reply p-4 {$replyClass} shadow-sm'>";
                    echo "<p class='font-medium text-gray-800'>" . ($isAdminReply ? "<strong>Admin:</strong> " : "<strong>User:</strong> ") . "{$reply['replyContent']}</p>";
                    echo "<p class='text-sm text-gray-500'><small>Posted on: {$reply['createdAt']}</small></p>";
                    echo "</div>";
                }
            }

            echo "</div>"; // Close replies section

            // Show reply section only if the last reply was from the admin
            if ($lastReplyIsAdmin && $isAdmin === false) {
                echo "<div class='reply-section mt-6 space-y-4' id='reply-section-{$commentID}'>";
                echo "<textarea id='replyContent-{$commentID}' required class='w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400' placeholder='Write your reply here...'></textarea>";
                echo "<div class='flex justify-center'>";
                echo "<button class='reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700' data-comment-id='{$commentID}'>Reply</button>";
                echo "</div>";
                echo "</div>";
            } else if ($isAdmin) {
                echo "<div class='admin-reply-section mt-6 space-y-4' id='admin-reply-section-{$commentID}'>";
                echo "<textarea id='adminReplyContent-{$commentID}' required class='w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400' placeholder='Write your reply here...'></textarea>";
                echo "<div class='flex justify-center'>";
                echo "<button class='admin-reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700' data-comment-id='{$commentID}'>Reply</button>";
                echo "</div>";
                echo "</div>";
            }

            echo "</div>"; // Close notification details
            echo "</div>"; // Close comment div
        }
    }
    ?>
</div>