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
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/profile.css">
</head>

<body>
  <div class="p-6 m-6 bg-white shadow-lg rounded-lg text-left pb-6 mt-20 sm:mt-15 lg:mt-5 md:mt-10">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6">Profile</h1>
    <?php if (!empty($user)): ?>
      <div class="profile-details">
        <div class="profile-pic">
          <img
            src="<?php echo !empty($user['profilePic']) && $user['profilePic'] !== '0x64656661756c742e6a7067' ? $user['profilePic'] : '../../images/default.jpeg'; ?>"
            alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
        </div>
        <div class="profile-info mt-4">
          <p class="mb-2"><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
          <p class="mb-2"><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
          <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
          <p class="mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
        <button onclick="openEditProfileModal()"
          class="font-bold py-2 px-4 rounded transition duration-300 ease-in-out transform hover:scale-105 editbtn">
          Edit Profile
        </button>
      </div>
    <?php else: ?>
      <p>User profile not available.</p>
    <?php endif; ?>
  </div>

  <!-- Edit Profile Modal -->
  <div id="editProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="min-h-screen px-4 text-center flex items-center justify-center">
      <div class="modal-content rounded-lg shadow-xl max-w-md w-full">
        <div class="modal-header">
          <h3 class="text-lg leading-6 font-medium text-white">Edit Profile</h3>
          <button type="button" id="closeEditProfileModalBtn" class="text-gray-400 hover:text-gray-500"
            onclick="closeEditProfileModal()">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" id="closeEditProfileModalBtn">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="modal-body p-6">
          <form id="editProfileForm" method="post" enctype="multipart/form-data">
            <div class="mb-6 text-center">
              <div class="relative inline-block">
                <img id="profilePreview"
                  src="<?php echo !empty($user['profilePic']) && $user['profilePic'] !== '0x64656661756c742e6a7067' ? $user['profilePic'] : '../../images/default.jpeg'; ?>"
                  alt="Profile Picture" class="w-24 h-24 rounded-full object-cover mx-auto">
                <label for="profilePic"
                  class="absolute bottom-0 right-0 bg-blue-500 rounded-full p-2 cursor-pointer hover:bg-blue-600 transition duration-200">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                </label>
                <input type="file" id="profilePic" name="profilePic" accept="image/*" class="hidden"
                  onchange="previewImage(this)">
              </div>
            </div>

            <div class="space-y-4">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName"
                  value="<?php echo htmlspecialchars($user['firstName']); ?>" required>
              </div>

              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName"
                  value="<?php echo htmlspecialchars($user['lastName']); ?>" required>
              </div>

              <div class="form-group">
                <label for="email">Email (Cannot be changed)</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                  readonly disabled>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" id="cancelEditProfileBtn" class="btn-cancel" onclick="closeEditProfileModal()">
            Cancel
          </button>
          <button type="submit" form="editProfileForm" class="btn-save">
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openEditProfileModal() {
      document.getElementById('editProfileModal').classList.remove('hidden');
    }

    function closeEditProfileModal() {
      document.getElementById('editProfileModal').classList.add('hidden');
    }

    // Add event listeners for closing the modal
    document.getElementById('closeEditProfileModalBtn').addEventListener('click', function () {
      closeEditProfileModal();
    });

    document.getElementById('cancelEditProfileBtn').addEventListener('click', function () {
      closeEditProfileModal();
    });

    console.log(document.getElementById('closeEditProfileModalBtn'));

    // Close modal when clicking outside
    document.getElementById('editProfileModal').addEventListener('click', function (e) {
      if (e.target === this) {
        closeEditProfileModal();
      }
    });

    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Handle form submission
    document.getElementById('editProfileForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('editProfile.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Profile updated successfully!');
            closeEditProfileModal();
            window.location.reload();
          } else {
            alert(data.message || 'Error updating profile');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the profile');
        });
    });
  </script>
</body>

</html>