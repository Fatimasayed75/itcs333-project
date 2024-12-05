<?php
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/utils/helpers.php';

$id = isAuthorized();
$userModel = new UserModel($pdo);
$user = $userModel->getUserByID($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    try {
        $firstName = htmlspecialchars(trim($_POST['firstName']));
        $lastName = htmlspecialchars(trim($_POST['lastName']));
        $email = htmlspecialchars(trim($_POST['email']));
        $profilePic = $user['profilePic'];

        // Handle profile picture upload
        if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['profilePic']['type'], $allowedTypes)) {
                throw new Exception('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
            }

            if ($_FILES['profilePic']['size'] > $maxFileSize) {
                throw new Exception('File is too large. Maximum size is 5MB.');
            }

            $uploadDir = __DIR__ . '/../../images/profiles/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['profilePic']['name'], PATHINFO_EXTENSION);
            $fileName = 'profile_' . $id . '_' . time() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadFile)) {
                $profilePic = '../../images/profiles/' . $fileName;
            } else {
                throw new Exception('Failed to upload profile picture.');
            }
        }

        // Update user details
        $userModel->update($id, $firstName, $lastName, $email, $profilePic);
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log($e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
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
    <div class="edit-profile-container p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Edit Profile</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editProfileForm" method="post" enctype="multipart/form-data" class="space-y-4">
            <div class="text-center mb-4">
                <div class="relative inline-block">
                    <img src="<?php echo !empty($user['profilePic']) && $user['profilePic'] !== '0x64656661756c742e6a7067' ? $user['profilePic'] : '../../images/default-profile.png'; ?>" 
                         alt="Profile Picture" 
                         class="w-24 h-24 rounded-full object-cover mx-auto mb-2">
                    <label for="profilePic" class="cursor-pointer absolute bottom-0 right-0 bg-blue-500 rounded-full p-1 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </label>
                    <input type="file" id="profilePic" name="profilePic" accept="image/*" class="hidden">
                </div>
            </div>

            <div class="space-y-3">
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

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-md text-sm font-medium hover:bg-blue-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    <script src="../../js/nav.js"></script>
    <script src="../../js/profile.js"></script>
</body>
</html>