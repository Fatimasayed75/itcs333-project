<?php
include __DIR__ . '/../db-connection.php';

function isAuthorized()
{
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  if (isset($_SESSION['active-user'])) {
    $id = $_SESSION['active-user'];
  } else {
    header('Location: ../../templates/layout/signbase.php');
    exit();
  }
  return $id;
}
