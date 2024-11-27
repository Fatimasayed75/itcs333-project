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

function formatBookingDetails($startTime, $endTime) {
  // Convert to DateTime objects
  $start = new DateTime($startTime);
  $end = new DateTime($endTime);

  // Date and Day format
  $date = $start->format('M d, Y');
  $day = $start->format('l');

  // Time format
  $startTimeFormatted = $start->format('g:i A');
  $endTimeFormatted = $end->format('g:i A');    

  // Duration calculation
  $duration = $start->diff($end);
  if ($duration->h > 0) {
      $durationFormatted = $duration->h . 'h and ' . $duration->i . ' min';
  } else {
      $durationFormatted = $duration->i . ' min';
  }

  return [
      'date' => $date,
      'day' => $day,
      'startTime' => $startTimeFormatted,
      'endTime' => $endTimeFormatted,
      'duration' => $durationFormatted
  ];
}

