<?php
require_once '../db-connection.php';
session_start();
if (isset($_SESSION['id'])){
  unserialize($_SESSION['id']);
  header("Location: ../../frontend/templates/layout/base.php");
}

echo "Welcome Guest!";
