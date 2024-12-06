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
  <link rel="stylesheet" href="../../css/notification.css" />
  <link rel="stylesheet" href="../../css/roomDetails.css" />
  <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet" />
  <!-- tailwind css framework  -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>

<body>
  <!-- Navbar Component -->
  <?php include "../components/nav.php"; ?>

  <!-- Main Content -->
  <div id="main-content" class="content lg:ml-24 md:ml-12">
    <?php include "../components/home.php" ?>
  </div>
  <!-- <script src="../../js/fetchNotification.js" defer></script> -->
  <script src="../../js/nav.js"></script>
  <script src="../../js/rebook.js"></script>
  <script src="../../js/roomViews.js"></script>
  <script src="../../js/roomDetails.js"></script>
  <script src="../../js/cancelBooking.js"></script>
  <script src="../../js/addComment.js"></script>
  <script src="../../js/reply.js"></script>
  <script src="../../js/adminReply.js"></script>
  <script src="../../js/search.js"></script>
  <script src="../../js/floorsNav.js"></script>
  <script src="../../js/filter.js"></script>
  <script src="../../js/dash.js"></script>
  <script src="../../js/roomDetails.js"></script>
  <script src="../../js/openLab.js"></script>
  <script src="../../js/booking.js"></script>
  <script src="../../js/profile.js"></script>
  <script src="../../js/modal.js"></script>
</body>

</html>