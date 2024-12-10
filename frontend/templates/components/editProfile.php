<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/utils/helpers.php';
require_once '../../../backend/utils/database-manager.php';

$id = isAuthorized();
$userModel = new UserModel($pdo);
$user = $userModel->getUserByID($id);
$dbManager = new DatabaseManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    try {
        $firstName = htmlspecialchars(trim($_POST['firstName']));
        $lastName = htmlspecialchars(trim($_POST['lastName']));
        // $email = htmlspecialchars(trim($_POST['email']));
        $profilePic = null;

        // Handle profile picture upload
        if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
            // Delete any old profile pictures
            $dbManager->deleteOldFiles($id, 'profile');

            // Upload new profile picture
            $fileId = $dbManager->uploadFile($_FILES['profilePic'], $id, 'profile');

            // Update user's profile with the new file ID
            $userModel->update($id, $firstName, $lastName, $user['email'], $fileId);
        } else {
            // Update user details without changing profile picture
            $userModel->update($id, $firstName, $lastName, $user['email'], null);
        }

        $response['success'] = true;
        $response['message'] = 'Profile updated successfully';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log('Edit Profile Error: ' . $e->getMessage());
    } finally {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../../css/profile.css">
</head>

<body>
    <div class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm">
        <div
            class="edit-profile-container w-full max-w-md mx-4 bg-white rounded-lg shadow-2xl dark:bg-dark-sidebar-color dark:text-gray-200 transform transition-all duration-300 ease-in-out scale-95 hover:scale-100">
            <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-dark-border-color">
                <h2 class="text-xl font-semibold text-white dark:text-gray-200">Edit Profile</h2>
                <button onclick="closeModal()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editProfileForm" method="POST" enctype="multipart/form-data" class="p-4 space-y-4">
                <div class="profile-pic-upload flex flex-col items-center mb-4">
                    <div class="relative w-32 h-32 mb-4">
                        <!-- This image will display the profile picture preview -->
                        <img id="profilePicPreview" src="../../images/default.jpg" alt="Profile Picture"
                            class="w-full h-full object-cover rounded-full border-3 border-gray-200 dark:border-dark-border-color shadow-md">

                        <label for="profilePicInput"
                            class="absolute bottom-0 right-0 bg-primary-color text-white rounded-full p-2 cursor-pointer hover:bg-primary-color-light transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            <input type="file" id="profilePicInput" name="profilePic" class="hidden" accept="image/*"
                                onchange="previewImage(this)">
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="firstName"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                        <input type="text" id="firstName" name="firstName"
                            value="<?php echo htmlspecialchars($user['firstName']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-color focus:border-primary-color dark:bg-dark-primary-color-light dark:border-dark-border-color dark:text-gray-200">
                    </div>
                    <div class="form-group">
                        <label for="lastName"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                        <input type="text" id="lastName" name="lastName"
                            value="<?php echo htmlspecialchars($user['lastName']); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-color focus:border-primary-color dark:bg-dark-primary-color-light dark:border-dark-border-color dark:text-gray-200">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-color focus:border-primary-color dark:bg-dark-primary-color-light dark:border-dark-border-color dark:text-gray-200"
                        readonly>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors dark:bg-dark-toggle-color dark:text-gray-200 dark:hover:bg-dark-border-color">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-color text-white rounded-md hover:bg-primary-color-light transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="../../js/nav.js"></script>
    <script src="../../js/profile.js"></script>
</body>

</html>