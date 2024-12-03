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
            $uploadDir = '../../images/';
            $uploadFile = $uploadDir . basename($_FILES['profilePic']['name']);
            if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $uploadFile)) {
                $profilePic = $uploadFile;
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
    <div class="profile-container">
        <h1>Edit Profile</h1>
        <form id="editProfileForm" method="post" enctype="multipart/form-data" onsubmit="loadContent('profile.php')">
                <div class="profile-pic">
                <img src="<?php echo !empty($user['profilePic']) ? htmlspecialchars($user['profilePic']) : '../../images/default-profile.png'; ?>" alt="Profile Picture">
                <input type="file" name="profilePic">
            </div>
            <div class="profile-info">
                <p><strong>First Name:</strong> <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required></p>
                <p><strong>Last Name:</strong> <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required></p>
                <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></p>
            </div>
            <div class="profile-actions">
                <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600">Save Changes</button>
            </div>
        </form>
    </div>
    <script src="../../js/nav.js"></script>
    <script src="../../js/profile.js"></script>
</body>
</html>