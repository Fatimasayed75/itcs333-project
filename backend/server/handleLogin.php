<?php
require_once '../db-connection.php';
require_once '../database/user-model.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["sign-in-btn"])) {
    $email = trim(mb_strtolower(htmlspecialchars($_POST["email"])));
    $inputPassword = $_POST["password"];

    // Instantiate UserModel and fetch user by email
    $userModel = new UserModel($pdo);
    $user = $userModel->getUserByEmail($email);

    // Verify the password
    if ($user && password_verify($inputPassword, $user['password'])) {
      $_SESSION['active-user'] = $user['userID'];
      // Clear the old input data for registration and sign-in
      header("Location: ../../frontend/templates/layout/base.php");
      exit;
    } else {
      $_SESSION['signin-error'] = "Invalid email or password!";
      $_SESSION['old-input-signin'] = ['email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    }
  }
}
