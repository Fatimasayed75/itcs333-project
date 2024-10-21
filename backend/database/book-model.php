<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';

use Utils\Constants;
use Utils\Crud;

class BookModel
{
  private $conn;
  public $bookingID;
  public $userID;
  public $roomID;
  public $bookingTime;
  public $startTime;
  public $endTime;

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
  }

  // Create a new booking
  public function save()
  {
    // Convert startTime and endTime to DateTime objects for comparison
    $startTime = new DateTime($this->startTime);
    $endTime = new DateTime($this->endTime);

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
    $columns = ['userID', 'roomID', 'bookingTime', 'startTime', 'endTime'];
    $values = [$this->userID, $this->roomID, $this->bookingTime, $this->startTime, $this->endTime];
    return $crud->create('bookings', $columns, $values);
  }

  // check for a specific room is a startTime and endTime are valid
  private function checkConflicts($roomID, $startTime, $endTime)
  {
    $crud = new Crud($this->conn);
    $condition = 'roomID = ? AND ((startTime < ? AND endTime > ?) OR (startTime < ? AND endTime > ?))';
    $result = $crud->read('bookings', [], $condition, $roomID, $endTime, $startTime, $startTime, $endTime);

    return !empty($result); // returns true if there's a conflict
  }

  // Delete a a booking
  public function delete()
  {
    $crud = new Crud($this->conn);

    // Check if the booking exists
    $currentBooking = $this->getBookingByID($this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    $condition = 'bookingID = ?';

    // If the record exists, delete it
    return $crud->delete('bookings', $condition, $this->bookingID);
  }


  // Get all bookings
  public function getAllBookings()
  {
    $crud = new Crud($this->conn);
    $result = $crud->read('bookings');

    // Check if there are no records
    return !empty($result) ? $result : Constants::NO_RECORDS;
  }

  // get bookings by a specific field
  private function getBookingsBy($field, $value)
  {
    $crud = new Crud($this->conn);
    $condition = "{$field} = ?";
    $result = $crud->read('bookings', [], $condition, $value);

    return !empty($result) ? $result : Constants::NO_RECORDS;
  }

  // Get a booking by bookingID
  public function getBookingByID()
  {
    return $this->getBookingsBy('bookingID', $this->bookingID);
  }

  // Get bookings by roomID
  public function getBookingsByRoomID($roomID)
  {
    return $this->getBookingsBy('roomID', $roomID);
  }

  // Get bookings by userID
  public function getBookingsByUserID($userID)
  {
    return $this->getBookingsBy('userID', $userID);
  }

  // Get bookings by bookingTime
  public function getBookingsByBookingTime($bookingTime)
  {
    return $this->getBookingsBy('bookingTime', $bookingTime);
  }

  // Get bookings by status
  public function getBookingsByStatus($status)
  {
    return $this->getBookingsBy('status', $status);
  }

  // Get bookings by date range (startTime and endTime should be in the format of DateTime objects)
  public function getBookingsByDateRange($startDate, $endDate)
  {
    $crud = new Crud($this->conn);

    // The condition ensures any booking that overlaps with the given date range is returned
    $condition = '(startTime <= ? AND endTime >= ?)';
    $result = $crud->read(
      'bookings',
      [],
      $condition,
      $endDate,
      $startDate
    );

    return !empty($result) ? $result : Constants::NO_RECORDS;
  }


  // Delete all bookings
  public function deleteAllBookings()
  {
    $crud = new Crud($this->conn);
    $condition = '1';
    return $crud->delete('bookings', $condition);
  }

  // Get total number of bookings
  public function getBookingsCount()
  {
    $crud = new Crud($this->conn);
    $result = $crud->read('bookings', ['COUNT(*) as count']);
    return $result[0]['count'] ?? 0;
  }

  // Get the number of bookings for a user ID
  public function getBookingsCountByUserID($userID)
  {
    $crud = new Crud($this->conn);
    $condition = 'userID = ?';
    $result = $crud->read('bookings', ['COUNT(*) as count'], $condition, $userID);
    return $result[0]['count'] ?? 0;
  }

  // Update the status of a booking
  public function updateStatus($status)
  {
    $crud = new Crud($this->conn);

    // Check if the booking exists
    $condition = 'bookingID = ?';
    $currentBooking = $this->getBookingsByID($this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    // If the record exists, delete
    $update = ['status' => $status];
    $condition = 'bookingID = ?';
    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }

  // change end time to later time
  public function extendBooking($newEndTime)
  {
    $currentBooking = $this->getBookingsByID($this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    $crud = new Crud($this->conn);

    $currentEndTime = new DateTime($currentBooking['endTime']);
    $newEndDateTime = new DateTime($newEndTime);

    // Check if the new endTime is after the current endTime
    if ($newEndDateTime <= $currentEndTime) {
      return Constants::INVALID_END_TIME; // Return an error if newEndTime is not after the currentEndTime
    }

    // Check if the new endTime conflicts with another booking
    if ($this->checkConflicts($currentBooking['roomID'], $currentBooking['startTime'], $newEndTime)) {
      return Constants::BOOKING_CONFLICT;
    }

    // Update the endTime if the newEndTime is valid
    $update = ['endTime' => $newEndTime];
    $condition = 'bookingID = ?';

    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }

  // update start and and dates (startTime and endTime should be in the format of DateTime objects)
  public function delayBooking($newStartTime, $newEndTime)
  {
    $crud = new Crud($this->conn);

    // Fetch the current booking by ID
    $currentBooking = $this->getBookingsByID($this->bookingID);

    if ($currentBooking === Constants::BOOKING_NOT_FOUND) {
      return Constants::BOOKING_NOT_FOUND;
    }

    // Convert new start and end times to DateTime objects
    $newStartDateTime = new DateTime($newStartTime);
    $newEndDateTime = new DateTime($newEndTime);

    // Check for conflicts with other bookings
    if ($this->checkConflicts($currentBooking['roomID'], $newStartDateTime, $newEndDateTime)) {
      return Constants::BOOKING_CONFLICT;
    }

    //Update the booking with the new start and end times
    $update = [
      'startTime' => $newStartDateTime,
      'endTime' => $newEndDateTime
    ];
    $condition = 'bookingID = ?';

    return $crud->update('bookings', $update, $condition, $this->bookingID);
  }

  // Expire past bookings
  public function expire()
  {
    $crud = new Crud($this->conn);
    $condition = 'bookingID = ? AND endTime < ? AND status = ?';
    $update = ['status' => 'canceled'];

    // Update the status to 'expired' where conditions match
    // date('Y-m-d H:i:s') is the current date and time
    echo date('Y-m-d H:i:s');
    getBookingByID();
    return $crud->update('bookings', $update, $condition, $this->bookingID, "2021-10-01 12:00:00", 'pending');
  }


  // Cancel a booking
  public function cancelBooking($bookingID)
  {
    return $this->updateStatus($bookingID, 'cancelled');
  }

}
