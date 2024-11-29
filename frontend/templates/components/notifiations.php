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

<div class="notifications p-6 space-y-6 bg-gray-100 rounded-lg pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800">Notifications</h1>

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
            <div class="comment p-4 bg-white rounded-lg shadow-md space-y-4" id="comment-<?= $commentID; ?>">
                <div class="notification-header flex justify-between items-center" data-comment-id="<?= $commentID; ?>">
                    <p class="text-lg font-medium text-gray-700">
                        <strong>Room:</strong> <?= $roomID; ?> | 
                        <strong>Date:</strong> <?= $bookingDate; ?>
                    </p>
                    <span class="text-sm text-gray-500">
                        <?= $startTime; ?> to <?= $endTime; ?>
                    </span>
                    <i class="expand-icon fa fa-chevron-down ml-2 cursor-pointer" onclick="toggleDetails(<?= $commentID; ?>, this)"></i>
                </div>

                <div class="notification-details mt-4 space-y-4 hidden" id="details-<?= $commentID; ?>">
                    <div class="reply p-4 bg-gray-50 border-l-4 border-gray-300 shadow-sm">
                        <p class="font-medium text-gray-800">
                            <strong>User Feedback:</strong> <?= $commentContent; ?>
                        </p>
                    </div>

                    <?php if (!empty($replies)): ?>
                        <div class="replies mt-4 space-y-4" id="replies-<?= $commentID; ?>">
                            <?php foreach ($replies as $index => $reply):
                                $isAdminReply = ($reply['userID'] == Constants::ADMIN_USER_ID);
                                if ($isAdminReply && $index === count($replies) - 1) {
                                    $lastReplyIsAdmin = true;
                                }
                                $replyClass = $isAdminReply ? 'bg-green-50 border-l-4 border-green-400' : 'bg-gray-50 border-l-4 border-gray-300';
                                ?>
                                <div class="reply p-4 <?= $replyClass; ?> shadow-sm">
                                    <p class="font-medium text-gray-800">
                                        <?= $isAdminReply ? '<strong>Admin:</strong> ' : '<strong>User:</strong> '; ?>
                                        <?= $reply['replyContent']; ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <small>Posted on: <?= $reply['createdAt']; ?></small>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($lastReplyIsAdmin && $isAdmin === false): ?>
                        <div class="reply-section mt-6 space-y-4" id="reply-section-<?= $commentID; ?>">
                            <textarea id="replyContent-<?= $commentID; ?>" required class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write your reply here..."></textarea>
                            <div class="flex justify-center">
                                <button class="reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" data-comment-id="<?= $commentID; ?>">Reply</button>
                            </div>
                        </div>
                    <?php elseif ($isAdmin): ?>
                        <div class="admin-reply-section mt-6 space-y-4" id="admin-reply-section-<?= $commentID; ?>">
                            <textarea id="adminReplyContent-<?= $commentID; ?>" required class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write your reply here..."></textarea>
                            <div class="flex justify-center">
                                <button class="admin-reply-button px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" data-comment-id="<?= $commentID; ?>">Reply</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
