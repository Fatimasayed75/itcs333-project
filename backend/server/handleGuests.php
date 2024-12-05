<?php
require_once '../db-connection.php';
session_start();
// if user is already logged in, destroy the session and redirect to the home page

if(isset($_SESSION['active-user'])) {
  session_destroy();
}

// 0 means Guest User (not registered)
$_SESSION['active-user'] = 0;
header("Location: ../../frontend/templates/layout/base.php");
