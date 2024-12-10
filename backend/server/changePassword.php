<?php
// Ensure no output is sent before headers
ob_start();

require_once '../database/user-model.php';
require_once '../utils/helpers.php';
require_once '../db-connection.php';

$id = isAuthorized(); // Ensure the user is logged in
$userModel = new UserModel($pdo);
$user = $userModel->getUserByID($id);

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $currentPassword = htmlspecialchars(trim($_POST['currentPassword']));
        $newPassword = htmlspecialchars(trim($_POST['newPassword']));
        $confirmPassword = htmlspecialchars(trim($_POST['confirmPassword']));

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $response['message'] = 'Some fields are empty or spaces!';
        } elseif (str_contains($newPassword, ' ')) {
            $response['message'] = 'Password cannot contain spaces!';
        } elseif ($newPassword !== $confirmPassword) {
            $response['message'] = 'Passwords do not match!';
        } elseif ($newPassword === $currentPassword) {
            $response['message'] = 'New password must be different from the current password!';
        } elseif (
            strlen($newPassword) < 8 ||
            !preg_match('/[A-Z]/', $newPassword) ||
            !preg_match('/[a-z]/', $newPassword) ||
            !preg_match('/[0-9]/', $newPassword) ||
            !preg_match('/[\W_]/', $newPassword)
        ) {
            $response['message'] = 'Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character!';
        } else {
            // Get the user's current password hash from the database
            $userCurrentPasswordHash = $userModel->getUserPassword($id);

            if (!password_verify($currentPassword, $userCurrentPasswordHash)) {
                $response['message'] = 'Current password is incorrect.';
            } else {
                // Hash the new password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                if ($userModel->updatePassword($id, $hashedNewPassword)) {
                    $response['success'] = true;
                    $response['message'] = 'Password updated successfully.';
                } else {
                    $response['message'] = 'Failed to update the password. Please try again.';
                }
            }
        }
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

// Clear the buffer and send a clean JSON response
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
