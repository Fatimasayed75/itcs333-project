<?php
require_once '../../../backend/db-connection.php';
session_start();

if (isset($_SESSION['active-user'])) {
  $id = $_SESSION['active-user'];
} else {
  header('Location: ../../templates/layout/signbase.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="../../css/nav.css" />
  <link rel="stylesheet" href="../../css/home.css" />
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
  <!-- tailwind css framework  -->
  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
  <!-- Navbar Component -->
  <?php include "../components/nav.php"; ?>

  <!-- Main Content -->
  <div id="main-content" class="pt-24 sm:pt-24 md:pt-16 lg:pt-16 ml-4 sm:ml-10 md:ml-40 lg:ml-40">
    <h1 class="text-xl sm:text-3xl lg:text-4xl font-bold text-gray-800 welcome-message">
      Welcome home, user with ID = <?php echo $id ?>
    </h1>
  </div>
  <script src="../../js/nav.js"></script>
</body>
</html>