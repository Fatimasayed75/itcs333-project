<?php
// Include necessary files
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/db-connection.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/constants.php';


USE Utils\Constants;

$id = isAuthorized();

// Instantiate models
$commentModel = new CommentModel($pdo);
$commentReplyModel = new CommentReplyModel($pdo);
$userModel = new UserModel($pdo);

// Fetch all comments made by users
$comments = $commentModel->getAllComments();

$userDetails = $userModel->getUserByID($id);

// Function to fetch replies to a specific comment
function getRepliesForComment($commentID, $commentReplyModel) {
    return $commentReplyModel->getRepliesByCommentID($commentID);
}
?>

<div class="notifications p-6 space-y-6 bg-gray-100 rounded-lg">
    <h1 class="text-3xl font-semibold text-gray-800">User Comments</h1>

    <?php
    // Loop through each comment and display
    foreach ($comments as $comment) {
        $commentID = $comment['commentID'];
        $userID = $comment['userID'];
        $commentContent = $comment['content'];
        $createdAt = $comment['createdAt'];

        echo "<div class='comment p-4 bg-white rounded-lg shadow-md space-y-4'>";
        echo "<p class='text-lg font-medium text-gray-800'><strong>User:</strong> {$userDetails['firstName']} {$userDetails['lastName']}</p>";
        echo "<p class='text-gray-700'><strong>Comment:</strong> $commentContent</p>";
        echo "<p class='text-sm text-gray-500'><small>Posted on: $createdAt</small></p>";

        // Fetch replies for this comment
        $replies = getRepliesForComment($commentID, $commentReplyModel);
        echo "<div class='replies mt-4 space-y-4'>";

        if (!empty($replies)) {
            foreach ($replies as $reply) {
                echo "<div class='reply p-4 bg-gray-50 border-l-4 border-gray-300 shadow-sm'>";
                echo "<p class='font-medium text-gray-800'><strong>Admin:</strong> {$reply['replyContent']}</p>";
                echo "<p class='text-sm text-gray-500'><small>Posted on: {$reply['createdAt']}</small></p>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-gray-500'>No replies yet.</p>";
        }

        echo "</div>";

        // If the user is an admin, show the reply form
        if ($userDetails['email'] === Constants::ADMIN_EMAIL) {
            echo "<form action='reply.php' method='POST' class='mt-6 space-y-4'>";
            echo "<input type='hidden' name='commentID' value='$commentID' />";
            echo "<textarea name='replyContent' required class='w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400' placeholder='Write your reply here...'></textarea>";
            echo "<button type='submit' class='px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-400'>Reply</button>";
            echo "</form>";
        }

        echo "</div>";
    }
    ?>
</div>
