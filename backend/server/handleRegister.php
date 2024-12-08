<?php
require_once '../db-connection.php';
require_once '../database/user-model.php';
use Utils\Constants;

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["sign-up-btn"])) {
    // ensure first name and last name are capitalized and email is lowercase
    $fname = ucfirst(mb_strtolower(htmlspecialchars(trim($_POST["firstName"]))));
    $lname = ucfirst(mb_strtolower(htmlspecialchars(trim($_POST["lastName"]))));
    $email = mb_strtolower(htmlspecialchars(trim($_POST["email"])));
    $password = trim($_POST["password"]); // Password doesn't need htmlspecialchars since it won't be rendered in HTML
    $role = "";

    if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
      $_SESSION['register-error'] = "Some fields are empty or spaces!";
      $_SESSION['old-input-reg'] = ['fname' => $fname, 'lname' => $lname, 'email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    } elseif (str_contains($password, " ")) {
      $_SESSION['register-error'] = "Password cannot contain spaces!";
      $_SESSION['old-input-reg'] = ['fname' => $fname, 'lname' => $lname, 'email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    } elseif (str_contains($fname, " ") || str_contains($lname, " ")) {
      $_SESSION['register-error'] = "First name and last name cannot contain spaces!";
      $_SESSION['old-input-reg'] = ['fname' => $fname, 'lname' => $lname, 'email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    } elseif (
      strlen($password) < 8 ||
      !preg_match('/[A-Z]/', $password) ||  // at least one uppercase letter
      !preg_match('/[a-z]/', $password) ||  // at least one lowercase letter
      !preg_match('/[0-9]/', $password) ||  // at least one digit
      !preg_match('/[\W_]/', $password) // at least one special character (non-word character _ ! @ # etc)
    ) {
      $_SESSION['register-error'] = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character!";
      $_SESSION['old-input-reg'] = ['fname' => $fname, 'lname' => $lname, 'email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    }

    // Determine user role based on email pattern
    if (preg_match("/^(201[0-9]|202[0-4])[0-9]{4,5}@stu\.uob\.edu\.bh$/", $email)) {
      $role = "student";
    } elseif (preg_match("/^[a-z]+@uob\.edu\.bh$/i", $email)) {
      $role = "instructor";
    } elseif ($email === Constants::ADMIN_EMAIL) {
      $role = "admin";
    } else {
      $_SESSION['register-error'] = "Invalid email address! Please use your UOB email.";
      $_SESSION['old-input-reg'] = ['fname' => $fname, 'lname' => $lname, 'email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    }

    // Instantiate UserModel and check if the email is already registered
    $userModel = new UserModel($pdo, null, $email, $password, $fname, $lname, $role);
    if ($userModel->getUserByEmail($email)) {
      $_SESSION['register-error'] = "This email is already registered!";
      $_SESSION['old-input-reg'] = ['fname' => $fname, 'lname' => $lname, 'email' => $email];
      header("Location: ../../frontend/templates/layout/signbase.php");
      exit;
    }

    // Save the new user
    $userId = $userModel->save();
    $_SESSION['active-user'] = $userId;
    // Clear the old input data for registration and sign-in
    header("Location: ../../frontend/templates/layout/base.php");
    exit;

  }
}
