<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';

use Utils\Constants;
use Utils\Crud;

// FORMAT FOR startTime / endTime is 
// 'Y-m-d H:i:s' e.g. 2021-10-29 10:00:00
class BookModel
{
  private $conn;
  public $bookingID;
  public $userID;
  public $roomID;
  public $bookingTime;
  public $startTime;
  public $endTime;
  public $status;
  public $feedback;

  // Constructor
  function __construct($conn, $userID, $roomID, $bookingTime, $startTime, $endTime, $bookingID = null)
{
    $this->conn = $conn;
    $this->bookingID = $bookingID;
    $this->userID = $userID;
    $this->roomID = $roomID;
    $this->bookingTime = $bookingTime;
    $this->startTime = $startTime;
    $this->endTime = $endTime;
    $this->status = 'active';
}

  // Create a new booking
  public function save()
  {
    // Convert startTime and endTime to DateTime objects for comparison
    $startTime = new DateTime($this->startTime);
    $endTime = new DateTime($this->endTime);

    // Booking must be at least after 1 hour
    $currentPlusOneHour = new DateTime();
    $currentPlusOneHour->modify('+1 hour');

    if ($startTime < $currentPlusOneHour) {
      return Constants::INVALID_START_TIME;
    }

    // Calculate booking time
    $interval = $startTime->diff($endTime);

    // Check if valid
    // Check if the duration is less than 30 minutes
    if ($interval->h == 0 && $interval->i < 30) {
      return Constants::BOOKING_DURATION_TOO_SHORT;
    }

    // Check if the duration exceeds 2 hours and 30 minutes
    if ($interval->h > 2 || ($interval->h == 2 && $interval->i > 30)) {
      return Constants::BOOKING_DURATION_TOO_LONG;
    }

    if ($this->checkConflicts($this->roomID, $this->startTime, $this->endTime)) {
      return Constants::BOOKING_CONFLICT;
    }

    // if booking duration valid, insert it
    $crud = new Crud($this->conn);
    $columns = ['userID', 'roomID', 'bookingTime', 'startTime', 'endTime', 'status', 'feedback'];
    $values = [
      $this->userID,
      $this->roomID,
      $this->bookingTime,
      $this->startTime,
      $this->endTime,
      $this->status = $this->roomID == "S40-1002" ? 'pending' : 'active' // Set status to 'pending' if roomID is 1002
    ];

    if ($crud->create('bookings', $columns, $values)) {
      // Fetch the last inserted ID if insert was successful
      $this->bookingID = $this->conn->lastInsertId(); // Set the new bookingID
    }
  }

  // check for a specific room is a startTime and endTime are valid
  private function checkConflicts($roomID, $startTime, $newEndTime)
  {
    $crud = new Crud($this->conn);

    // 10 MINUTES GAP BETWEEN BOOKINGS
    $condition = 'roomID = ? AND status = ? AND (
      (endTime + INTERVAL 10 MINUTE > ?) AND (startTime < ?)
      OR (endTime + INTERVAL 10 MINUTE > ?) AND (startTime < ?)
    )';

    // Case 1: New booking starts before an existing booking ends
    // Case 2: New booking ends after an existing booking starts
    // Case 3: New booking is fully within an existing booking

    $result = $crud->read('bookings', [], $condition, $roomID, 'active', $startTime, $newEndTime, $startTime, $newEndTime);

    return !empty($result); // returns true if there's a conflict
  }

  // Delete a a booking
  public function delete($bookingID)
  {
    $crud = new Crud($this->conn);

    // Check if the booking exists
    $currentBooking = $this->getBookingsBy('bookingID', $bookingID);

    if ($currentBooking === Constants::NO_RECORDS) {
      return Constants::NO_RECORDS;
    }

    $condition = 'bookingID = ?';

    // If the record exists, delete it
    return $crud->delete('bookings', $condition, $bookingID);
  }


  // get bookings by a specific field
  private function getBookingsBy($field, $value)
  {
    $crud = new Crud($this->conn);
    $condition = "{$field} = ?";
    $result = $crud->read('bookings', [], $condition, $value);

    return !empty($result) ? $result : Constants::NO_RECORDS;
  }

  // Update the status of a booking
  public function updateStatus($status)
  {
    $crud = new Crud($this->conn);

    // Check if the booking exists
    $condition = 'bookingID = ?';
    $currentBooking = $this->getBookingsBy('bookingID', $this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    // If the record exists, update the status
    $update = ['status' => $status];
    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }


  // change end time to later time
  public function extendBooking($newEndTime)
  {
    $currentBooking = $this->getBookingsBy('bookingID', $this->bookingID);

    if ($currentBooking === Constants::NO_RECORDS) {
      return Constants::BOOKING_NOT_FOUND;
    }

    if (!is_array($currentBooking)) {
      return Constants::FAILED;
    }

    $crud = new Crud($this->conn);

    $currentEndTime = new DateTime($currentBooking[0]['endTime']);
    $newEndDateTime = new DateTime($newEndTime);

    // Check if the new endTime is after the current endTime
    if ($newEndDateTime <= $currentEndTime) {
      return Constants::INVALID_END_TIME; // Return an error if newEndTime is not after the currentEndTime
    }

    // Check if the new endTime conflicts with another booking
    if ($this->checkConflicts($currentBooking[0]['roomID'], $currentBooking[0]['startTime'], $newEndTime)) {
      return Constants::BOOKING_CONFLICT;
    }

    // Update the endTime if the newEndTime is valid
    $update = ['endTime' => $newEndTime];
    $condition = 'bookingID = ?';

    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }

  // update start and and dates (startTime and endTime should be in the format of DateTime objects)
  public function changeBookingTime($newStartTime, $newEndTime)
  {
    $crud = new Crud($this->conn);

    // Fetch the current booking by ID
    $currentBooking = $this->getBookingsBy('bookingID', $this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    // Check for conflicts with other bookings
    if ($this->checkConflicts($currentBooking[0]['roomID'], $newStartTime, $newEndTime)) {
      return Constants::BOOKING_CONFLICT;
    }

    //Update the booking with the new start and end times
    $update = [
      'startTime' => $newStartTime,
      'endTime' => $newEndTime
    ];
    $condition = 'bookingID = ?';

    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }

  // Expire the booking
  public function expire()
  {
    $crud = new Crud($this->conn);
    $condition = 'bookingID = ? AND endTime < ? AND status = ?';
    $update = ['status' => 'expired'];

    $currentDateTime = (new DateTime())->format('Y-m-d H:i:s');

    // Update the status to 'expired' where conditions match
    // date('Y-m-d H:i:s') is the current date and time
    return $crud->update('bookings', $update, $condition, $this->bookingID, $currentDateTime, "active");
  }

  // Get Upcoming Bookings
  public function getUpcomingBookingsByUser($userID)
  {
    $crud = new Crud($this->conn);
    $currentTime = (new DateTime())->format('Y-m-d H:i:s');

    // Query to get upcoming bookings ordered by startTime ASC
    $condition = 'userID = ? AND startTime > ? AND status = ? ORDER BY startTime ASC';
    $result = $crud->read('bookings', [], $condition, $userID, $currentTime, 'active');

    return !empty($result) ? $result : [];
  }


  // Get Current Bookings
  public function getCurrentBookingsByUser($userID)
  {
    $crud = new Crud($this->conn);
    $currentTime = (new DateTime())->format('Y-m-d H:i:s');

    // Query to get current bookings ordered by startTime DESC
    $condition = 'userID = ? AND startTime <= ? AND endTime >= ? AND status = ? ORDER BY startTime DESC';
    $result = $crud->read('bookings', [], $condition, $userID, $currentTime, $currentTime, 'active');

    return !empty($result) ? $result : [];
  }



  // Get Previous Bookings
  public function getPreviousBookingsByUser($userID)
  {
    $crud = new Crud($this->conn);
    $currentTime = (new DateTime())->format('Y-m-d H:i:s');

    // Query to get previous bookings ordered by endTime DESC
    $condition = 'userID = ? AND endTime < ? ORDER BY endTime DESC';
    $result = $crud->read('bookings', [], $condition, $userID, $currentTime);

    return !empty($result) ? $result : [];
  }

  // Check if feedback has already been submitted for the booking
  public function hasFeedbackSubmitted()
  {
    $crud = new Crud($this->conn);
    $condition = 'bookingID = ?';
    $result = $crud->read('bookings', ['feedback'], $condition, $this->bookingID);

    if (!empty($result)) {
      return $result[0]['feedback'] == 1;
    }

    return false;
  }

  // Update feedback submission status
  public function submitFeedback()
  {
    $crud = new Crud($this->conn);

    // Check if the booking exists
    $condition = 'bookingID = ?';
    $currentBooking = $this->getBookingsBy('bookingID', $this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    // If feedback is already submitted, return false
    if ($this->hasFeedbackSubmitted()) {
      return false;
    }

    // If feedback hasn't been submitted yet, update it
    $update = ['feedback' => 1];
    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }

  public function getAllBookings()
  {
    $crud = new Crud($this->conn);
    $currentTime = (new DateTime())->format('Y-m-d H:i:s');

    // Query to get all past bookings ordered by endTime DESC
    $condition = 'endTime < ? ORDER BY endTime DESC';
    $result = $crud->read('bookings', [], $condition, $currentTime);

    return !empty($result) ? $result : [];
  }

  public function getBookingByID($bookingID) {
   
    $currentBooking = $this->getBookingsBy('bookingID', $bookingID);

    if ($currentBooking === Constants::NO_RECORDS) {
      return Constants::NO_RECORDS;
    }

    return $currentBooking;

  }

  // Get the total number of bookings
  public function getTotalBookings(): mixed
  {
      $crud = new Crud($this->conn);
      $result = $crud->read('bookings', ['COUNT(*) as count']);
      return $result[0]['count'] ?? 0;
  }

  // Get booking statistics by month
  function getBookingsByMonth() {
    // SQL query to get bookings by year and month
    $query = "
        SELECT YEAR(bookingTime) AS year, MONTH(bookingTime) AS month, COUNT(*) AS booking_count
        FROM bookings
        GROUP BY YEAR(bookingTime), MONTH(bookingTime)
        ORDER BY year DESC, month DESC
    ";

    // Prepare and execute the query
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    // Fetch the result and return it as an associative array
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

 
function getBookingsByDepartment() {
  // SQL query to get bookings by department
  $query = "
      SELECT r.department, COUNT(b.bookingID) AS booking_count
      FROM bookings b
      JOIN room r ON b.roomID = r.roomID
      GROUP BY r.department
  ";

  // Prepare and execute the query
  $stmt = $this->conn->prepare($query);
  $stmt->execute();

  // Fetch the result and return it as an associative array
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getMostBookedRoom() {
  // SQL query to get the most booked room
  $query = "
      SELECT roomID, COUNT(*) AS count
      FROM bookings
      GROUP BY roomID
      ORDER BY count DESC
      LIMIT 1
  ";

  // Prepare and execute the query
  $stmt = $this->conn->prepare($query);
  $stmt->execute();

  // Fetch the result and return the room_id
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  return $result['roomID'] ?? null; // Return the room ID with the most bookings
}



}
