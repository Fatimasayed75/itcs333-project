<?php
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/book-model.php';
require_once '../../../backend/db-connection.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';

USE Utils\Constants;

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

// Fetch the booking details for the current user (based on roomID)
$bookingDetails = $bookModel->getPreviousBookingsByUser($id);

// Prepare an associative array to map roomID to its booking details
$bookingDetailsByRoom = [];
foreach ($bookingDetails as $booking) {
    $bookingDetailsByRoom[$booking['roomID']][] = $booking;  // Multiple bookings per room
}

?>

<div class="notifications p-6 space-y-6 bg-gray-100 rounded-lg pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800">Notifications</h1>

    <?php
    // Loop through each comment and display only those that have an admin reply
   // Loop through each comment and display only those that have an admin reply
foreach ($comments as $comment) {
    $commentID = $comment['commentID'];
    $userID = $comment['userID'];
    $roomID = $comment['roomID'];
    $commentContent = $comment['content'];
    $createdAt = $comment['createdAt'];

    // Check if the comment has a reply from the admin
    if ($commentModel->hasAdminReply($commentID)) {

        echo "<div class='comment p-4 bg-white rounded-lg shadow-md space-y-4' id='comment-{$commentID}'>";

        // Display booking details only if bookings exist for this room
        $bookingsForRoom = isset($bookingDetailsByRoom[$roomID]) ? $bookingDetailsByRoom[$roomID] : null;
        if ($bookingsForRoom) {
            foreach ($bookingsForRoom as $booking) {
                // Format booking details
                $formattedDetails = formatBookingDetails($booking['startTime'], $booking['endTime']);
                
                // Display formatted booking details
                echo "<p class='text-gray-700'><strong>Room:</strong> {$booking['roomID']}</p>";
                echo "<p class='text-gray-700'><strong>Date:</strong> {$formattedDetails['date']}</p>";
                echo "<p class='text-gray-700'><strong>Day:</strong> {$formattedDetails['day']}</p>";
                echo "<p class='text-gray-700'><strong>Booking Time:</strong> {$formattedDetails['startTime']} to {$formattedDetails['endTime']}</p>";
                echo "<p class='text-gray-700'><strong>Duration:</strong> {$formattedDetails['duration']}</p>";
            }
        }

        // Display comment content
        echo "<div class='reply p-4 bg-gray-50 border-l-4 border-gray-300 shadow-sm'>";
        echo "<p class='font-medium text-gray-800'><strong>User Comment:</strong> {$commentContent}</p>";
        echo "<p class='text-sm text-gray-500'><small>Posted on: {$createdAt}</small></p>";
        echo "</div>"; // Close comment content styled like a reply

        // Display replies for this comment
        echo "<div class='replies mt-4 space-y-4' id='replies-{$commentID}'>";

        $replies = $commentReplyModel->getRepliesByCommentID($commentID);
        $isAdminReplyFound = false; // Flag to check if an admin reply is found
        $lastReplyIsAdmin = false; // Flag to track if the last reply is from the admin

        if (!empty($replies)) {
            foreach ($replies as $index => $reply) {
                // Check if the reply is from the admin
                $isAdminReply = ($reply['userID'] == Constants::ADMIN_USER_ID);
                if ($isAdminReply && $index === count($replies) - 1) {
                    $lastReplyIsAdmin = true;
                }

                $replyClass = $isAdminReply ? 'bg-green-50 border-l-4 border-green-400' : 'bg-gray-50 border-l-4 border-gray-300';
                echo "<div class='reply p-4 {$replyClass} shadow-sm'>";
                echo "<p class='font-medium text-gray-800'>" . ($isAdminReply ? "<strong>Admin:</strong> " : "<strong>User:</strong> ") . "{$reply['replyContent']}</p>";
                echo "<p class='text-sm text-gray-500'><small>Posted on: {$reply['createdAt']}</small></p>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-gray-500'>No replies yet.</p>";
        }

        echo "</div>"; // Close replies section

        // Initially hide the reply section by default (we'll control visibility with JS)
        // Show reply section only if the last reply was from the admin
        if ($lastReplyIsAdmin) {
            echo "<div class='reply-section mt-6 space-y-4' id='reply-section-{$commentID}'>";
            echo "<textarea id='replyContent-{$commentID}' required class='w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400' placeholder='Write your reply here...'></textarea>";
            echo "<div class='flex justify-center'>";  // Add this div to center the button
            echo "<button class='reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-400' data-comment-id='{$commentID}'>Reply</button>";
            echo "</div>";  // Close the center div
            echo "</div>"; // Close reply section
        }
        
        echo "</div>"; // Close comment div
    }
}

    ?>
</div>
