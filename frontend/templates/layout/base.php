<?php
require_once '../../../backend/db-connection.php';
session_start();

$id = $_SESSION["active-user"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
</head>
<body>
  <h1>Welcome to home user with id = <?php echo $id?></h1>
</body>
</html>