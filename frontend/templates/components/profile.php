<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/database/room-model.php';
require_once '../../../backend/database/comment-model.php';
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/database-manager.php';

$id = isAuthorized();
$userModel = new UserModel($pdo);
$roomModel = new RoomModel($pdo);
$commentModel = new CommentModel($pdo);
$commentReplyModel = new CommentReplyModel($pdo);
$dbManager = new DatabaseManager($pdo);

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
          <?php if (isset($user['profilePicData'])): ?>
            <img src="data:<?php echo $user['profilePicData']['mime_type']; ?>;base64,<?php
               echo base64_encode($user['profilePicData']['file_content']); ?>" alt="Profile Picture"
              class="w-24 h-24 rounded-full object-cover">
          <?php else: ?>
            <img src="../../images/default.jpg" alt="TTTTTTTTTTTTTTTTTTTTTT" class="w-24 h-24 rounded-full object-cover">
          <?php endif; ?>
        </div>
        <div class="profile-info mt-4">
          <p class="mb-2"><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
          <p class="mb-2"><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
          <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
          <p class="mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
        <div class="profile-actions mt-4 flex gap-4">
          <button onclick="openEditProfileModal()"
            class="font-bold py-2 px-4 rounded-md bg-blue-500 hover:bg-blue-600 text-white transition duration-300 ease-in-out">
            Edit Profile
          </button>
          <button onclick="openChangePasswordModal()"
            class="font-bold py-2 px-4 rounded-md bg-blue-500 hover:bg-blue-600 text-white transition duration-300 ease-in-out">
            Change Password
          </button>
        </div>
      </div>
    <?php else: ?>
      <p>User profile not available.</p>
    <?php endif; ?>
  </div>

  <!-- Edit Profile Modal -->
  <div id="editProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="min-h-screen px-4 text-center flex items-center justify-center">
      <div class="modal-content rounded-lg shadow-xl max-w-[250px] w-full m-2">
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

        <div class="modal-body p-3">
          <form id="editProfileForm" method="post" enctype="multipart/form-data">
            <div class="mb-3 text-center">
              <div class="relative inline-block">
                <?php if (isset($user['profilePicData'])): ?>
                  <img
                    id="profilePicPreview"
                    src="data:<?php echo $user['profilePicData']['mime_type']; ?>;base64,<?php echo base64_encode($user['profilePicData']['file_content']); ?>"
                    alt="Profile Picture" class="w-20 h-20 rounded-full object-cover mx-auto">
                  <!-- Reduced size from w-20 h-20 to w-16 h-16 -->
                <?php else: ?>
                  <img src="../../images/default.jpg" alt="Profile Picture"
                    class="w-20 h-20 rounded-full object-cover mx-auto" id="profilePicPreview">
                <?php endif; ?>
                <label for="profilePic"
                  class="absolute bottom-0 right-0 bg-blue-500 rounded-full p-1 cursor-pointer hover:bg-blue-600 transition duration-200">
                  <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                </label>
                <input type="file" id="profilePic" name="profilePic" accept="image/*" class="hidden"
                  onchange="previewImage(this)">
              </div>
            </div>

            <div class="space-y-3">
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
          <button type="button" id="cancelEditProfileBtn" class="btn-cancel"
            onclick="closeEditProfileModal()">Cancel</button>
          <button type="submit" form="editProfileForm" class="btn-save">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg w-96 p-6 shadow-lg relative">
    <button onclick="closeChangePasswordModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">
      <i class="bx bx-x"></i>
    </button>

    <h2 class="text-xl font-semibold text-center mb-6">Change Password</h2>

    <form id="changePasswordForm" method="post">
      <!-- Current Password -->
      <div class="mb-4">
        <label for="currentPassword" class="block text-sm font-medium text-gray-700">Current Password</label>
        <input type="password" id="currentPassword" name="currentPassword" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
      </div>

      <!-- New Password -->
      <div class="mb-4">
        <label for="newPassword" class="block text-sm font-medium text-gray-700">New Password</label>
        <input type="password" id="newPassword" name="newPassword" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
      </div>

      <!-- Confirm New Password -->
      <div class="mb-6">
        <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required />
      </div>

      <!-- Error Message -->
      <div id="errorMessage" class="hidden text-[#D885A3] text-sm mb-4"></div>

      <div class="flex justify-between">
        <button type="button" onclick="closeChangePasswordModal()" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-[#D885A3] text-white rounded-md hover:bg-[#C77492] transition duration-300" form="changePasswordForm">Update Password</button>
      </div>
    </form>
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