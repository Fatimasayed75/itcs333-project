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

  // Constructor
  function __construct($conn, $bookingID = null, $userID, $roomID, $bookingTime, $startTime, $endTime)
  {
    $this->conn = $conn;
    $this->bookingID = $bookingID;
    $this->userID = $userID;
    $this->roomID = $roomID;
    $this->bookingTime = $bookingTime;
    $this->startTime = $startTime;
    $this->endTime = $endTime;
    $this->status = 'pending';
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

    // if ($this->checkConflicts($this->roomID, $this->startTime, $this->endTime)) {
    //   return Constants::BOOKING_CONFLICT;
    // }

    // if booking duration valid, insert it
    $crud = new Crud($this->conn);
    $columns = ['userID', 'roomID', 'bookingTime', 'startTime', 'endTime'];
    $values = [$this->userID, $this->roomID, $this->bookingTime, $this->startTime, $this->endTime];
    if ($crud->create('bookings', $columns, $values)) {
      // Fetch the last inserted ID if insert was successful
      $this->bookingID = $this->conn->lastInsertId(); // Set the new bookingID
    }

  }

  // check for a specific room is a startTime and endTime are valid
  private function checkConflicts($roomID, $startTime, $endTime)
  {
    $crud = new Crud($this->conn);
    $condition = 'roomID = ? AND ((startTime < ? AND endTime > ?) OR (startTime < ? AND endTime > ?) OR (startTime >= ? AND endTime <= ?))';
    $result = $crud->read('bookings', [], $condition, $roomID, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime);

    return !empty($result); // returns true if there's a conflict
  }

  // Delete a a booking
  public function delete()
  {
    $crud = new Crud($this->conn);

    // Check if the booking exists
    $currentBooking = $this->getBookingsBy('bookingID', $this->bookingID);

    if ($currentBooking === Constants::NO_RECORDS) {
      return Constants::NO_RECORDS;
    }

    $condition = 'bookingID = ?';

    // If the record exists, delete it
    echo "deleting bookingID: {$this->bookingID}";
    return $crud->delete('bookings', $condition, $this->bookingID);
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
    var_dump($currentBooking);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    // If the record exists, update the status
    $update = ['status' => $status];
    echo "updating status to {$update['status']}";
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
      echo "currentBooking is not an array";
      return;
    }

    $crud = new Crud($this->conn);

    $currentEndTime = new DateTime($currentBooking[0]['endTime']);
    $newEndDateTime = new DateTime($newEndTime);

    // Check if the new endTime is after the current endTime
    if ($newEndDateTime <= $currentEndTime) {
      echo "newEndTime {$newEndDateTime->format('Y-m-d H:i:s')} is not after currentEndTime {$currentEndTime->format('Y-m-d H:i:s')}";
      return Constants::INVALID_END_TIME; // Return an error if newEndTime is not after the currentEndTime
    }

    // Check if the new endTime conflicts with another booking
    // if ($this->checkConflicts($currentBooking[0]['roomID'], $currentBooking[0]['startTime'], $newEndTime)) {
    //   return Constants::BOOKING_CONFLICT;
    // }

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
    // if ($this->checkConflicts($currentBooking['roomID'], $newStartDateTime, $newEndDateTime)) {
    //   return Constants::BOOKING_CONFLICT;
    // }

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

    var_dump($this->bookingID, $currentDateTime);

    // Update the status to 'expired' where conditions match
    // date('Y-m-d H:i:s') is the current date and time
    return $crud->update('bookings', $update, $condition, $this->bookingID, $currentDateTime, "active");
  }

  // Cancel a booking
  public function cancelBooking()
  {
    return $this->updateStatus('expired');
  }
}
