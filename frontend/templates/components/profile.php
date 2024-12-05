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
          <img src="<?php echo !empty($user['profilePic']) && $user['profilePic'] !== '0x64656661756c742e6a7067' ? $user['profilePic'] : '../../images/default-profile.png'; ?>" 
               alt="Profile Picture" 
               class="w-24 h-24 rounded-full object-cover">
        </div>
        <div class="profile-info mt-4">
          <p class="mb-2"><strong>First Name:</strong> <?php echo htmlspecialchars($user['firstName']); ?></p>
          <p class="mb-2"><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lastName']); ?></p>
          <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
          <p class="mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
      </div>
      <div class="profile-actions mt-6">
        <button onclick="openEditProfileModal()" 
                class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition duration-200">
          Edit Profile
        </button>
      </div>
    <?php else: ?>
      <p>User profile not available.</p>
    <?php endif; ?>
  </div>

  <!-- Edit Profile Modal -->
  <div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="min-h-screen px-4 text-center">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>

      <!-- Modal panel -->
      <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Profile</h3>
              
              <form id="editProfileForm" method="post" enctype="multipart/form-data">
                <div class="mb-6 text-center">
                  <div class="relative inline-block">
                    <img id="profilePreview" 
                         src="<?php echo !empty($user['profilePic']) && $user['profilePic'] !== '0x64656661756c742e6a7067' ? $user['profilePic'] : '../../images/default-profile.png'; ?>" 
                         alt="Profile Picture" 
                         class="w-24 h-24 rounded-full object-cover mx-auto">
                    <label for="profilePic" class="absolute bottom-0 right-0 bg-blue-500 rounded-full p-2 cursor-pointer hover:bg-blue-600 transition duration-200">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                      </svg>
                    </label>
                    <input type="file" id="profilePic" name="profilePic" accept="image/*" class="hidden" onchange="previewImage(this)">
                  </div>
                </div>

                <div class="space-y-4">
                  <div>
                    <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="firstName" name="firstName" 
                           value="<?php echo htmlspecialchars($user['firstName']); ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                  </div>

                  <div>
                    <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="lastName" name="lastName" 
                           value="<?php echo htmlspecialchars($user['lastName']); ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                  </div>

                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="button" onclick="document.getElementById('editProfileForm').submit()" 
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 text-base font-medium text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
            Save Changes
          </button>
          <button type="button" onclick="closeEditProfileModal()" 
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openEditProfileModal() {
      document.getElementById('editProfileModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeEditProfileModal() {
      document.getElementById('editProfileModal').classList.add('hidden');
      document.body.style.overflow = 'auto';
    }

    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Close modal when clicking outside
    document.getElementById('editProfileModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEditProfileModal();
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeEditProfileModal();
      }
    });

    // Handle form submission
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('components/editProfile.php', {
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