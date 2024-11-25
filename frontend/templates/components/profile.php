<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/room-model.php';
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/utils/helpers.php';

$id = isAuthorized();
$userModel = new UserModel($pdo);
$roomModel = new RoomModel($pdo);
$commentModel = new CommentModel($pdo);
$commentReplyModel = new CommentReplyModel($pdo);

$user = $userModel->getUserByID($id);
$rooms = $roomModel->getRoomsByUserId($id);
$comments = $commentModel->getCommentsByUserID($id);
$replies = $commentReplyModel->getRepliesByUserID($id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" href="../../css/profile.css">
</head>

<body>
  <div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Profile</h1>
    <?php if (!empty($user)): ?>
      <div class="profile-details">
        <div class="profile-pic">
        <!-- <img src="<?php echo !empty($user['profilePic']) ? htmlspecialchars($user['profilePic']) : '../../images/default-profile.jpeg'; ?>" alt="Profile Picture"> -->
        <img src="../../images/default-profile.jpeg" alt="Profile Picture">
        </div>
        <div class="profile-info">
          <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
          <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
          <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
      </div>
      <div class="profile-actions">
        <button class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600" onclick="editProfile()">Edit
          Profile</button>
      </div>
    <?php else: ?>
      <p>User profile not available.</p>
    <?php endif; ?>
  </div>
  <div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Rooms</h1>
    <!-- <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-4">Rooms</h2> -->
    <?php if (!empty($rooms)): ?>
      <?php if (is_array($rooms) && !empty($rooms)): ?>
        <ul>
          <?php foreach ($rooms as $room): ?>
            <li><?php echo htmlspecialchars($room['roomID']); ?> - <?php echo htmlspecialchars($room['type']); ?> -
              <?php echo htmlspecialchars($room['capacity']); ?> -
              <?php echo htmlspecialchars($room['isAvailable'] ? 'Available' : 'Not Available'); ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No rooms booked.</p>
      <?php endif; ?>
    <?php else: ?>
      <p>No rooms booked.</p>
    <?php endif; ?>
  </div>
  <div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Comments</h1>
    <!-- <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-4">Comments</h2> -->
    <?php if (!empty($comments)): ?>
      <ul>
        <?php if (is_array($comments) && !empty($comments)): ?>
          <ul>
            <?php foreach ($comments as $comment): ?>
              <li><?php echo htmlspecialchars($comment['content']); ?> -
                <?php echo htmlspecialchars($comment['createdAt']); ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No comments made.</p>
        <?php endif; ?>
      </ul>
    <?php else: ?>
      <p>No comments made.</p>
    <?php endif; ?>
  </div>
  <div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Replies</h1>
    <!-- <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-4">Replies</h2> -->
    <?php if (!empty($replies)): ?>
      <ul>
        <?php if (is_array($replies) && !empty($replies)): ?>
          <ul>
            <?php foreach ($replies as $reply): ?>
              <li><?php echo htmlspecialchars($reply['replyContent']); ?> -
                <?php echo htmlspecialchars($reply['createdAt']); ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No replies made.</p>
        <?php endif; ?>
      </ul>
    <?php else: ?>
      <p>No replies made.</p>
    <?php endif; ?>
  </div>

  <script>
    function editProfile() {
      // Redirect to edit profile page or open a modal for editing
      window.location.href = 'editProfile.php';
    }
  </script>
</body>

</html>