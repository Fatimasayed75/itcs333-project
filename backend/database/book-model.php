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

     // Check if the day is Friday
     if ($startTime->format('N') == 5) { // 5 corresponds to Friday in ISO-8601 (1 = Monday, 7 = Sunday)
      return Constants::INVALID_BOOKING_DAY; // Define this constant to represent the error
    }

    if($startTime->format('H:i') < '08:00') {
      return Constants::INVALID_START_TIME;
    }

    if($endTime->format('H:i') > '18:00') {
      return Constants::INVALID_END_TIME;
    }

    $currentDateTime = new DateTime();
    if ($startTime < $currentDateTime) {
        return Constants::START_TIME_IN_PAST; 
    }

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

    if ($this->checkConflict($this->roomID, $this->startTime, $this->endTime, $this->userID)) {
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
      $this->status = ($this->roomID == "S40-1002" || $this->roomID == "S40-2001") ? 'pending' : 'active',
      $this->feedback = 0
    ];

    if ($crud->create('bookings', $columns, $values)) {
      $this->bookingID = $this->conn->lastInsertId();
      return true;  // Return true if the insert was successful
   } else {
      return false; // Return false if the insert failed
   }
   
  }

  private function checkConflict($roomID, $startTime, $newEndTime, $userID)
  {
    $crud = new Crud($this->conn);

    // 10 MINUTES GAP BETWEEN BOOKINGS
    $condition = '(
        (roomID = ? OR userID = ?) AND status = ? AND (
            (endTime + INTERVAL 10 MINUTE > ? AND startTime < ?) OR
            (endTime > ? AND startTime - INTERVAL 10 MINUTE < ?) OR
            (startTime >= ? AND endTime <= ?)
        )
    )';

    // Case 1: New booking starts before an existing booking ends
    // Case 2: New booking ends after an existing booking starts
    // Case 3: New booking is fully within an existing booking

    // Check conflicts for roomID and userID
    $params = [
      $roomID,
      $userID,
      'active',
      $startTime,
      $newEndTime,
      $startTime,
      $newEndTime,
      $startTime,
      $newEndTime
    ];

    $result = $crud->read('bookings', [], $condition, ...$params);

    return !empty($result); // Return true if there's any conflict
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
    $condition = 'userID = ? AND endTime < ? AND status = "expired" ORDER BY endTime DESC';
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
    $condition = 'endTime < ? AND status = "active" ORDER BY endTime DESC';
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
      
      //count only 'active' or 'expired' bookings
      $query = "
          SELECT COUNT(*) as count
          FROM bookings
          WHERE status IN ('active', 'expired')
      ";
      
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
  
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['count'] ?? 0;
  }
  

  // Get booking statistics by month
  function getBookingsByMonth() {
    $query = "
        SELECT YEAR(bookingTime) AS year, MONTH(bookingTime) AS month, COUNT(*) AS booking_count
        FROM bookings
        WHERE status IN ('active', 'expired')
        GROUP BY YEAR(bookingTime), MONTH(bookingTime)
        ORDER BY year DESC, month DESC
    ";

    // Prepare and execute the query
    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
function getBookingsByDepartment() {
  $query = "
      SELECT r.department, COUNT(b.bookingID) AS booking_count
      FROM bookings b
      JOIN room r ON b.roomID = r.roomID
      WHERE b.status IN ('active', 'expired')
      GROUP BY r.department
  ";

  // Prepare and execute the query
  $stmt = $this->conn->prepare($query);
  $stmt->execute();

  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMostBookedRoom() {
  $query = "
      SELECT roomID, COUNT(*) AS count
      FROM bookings
      WHERE status IN ('active', 'expired')
      GROUP BY roomID
      ORDER BY count DESC
      LIMIT 1
  ";

  // Prepare and execute the query
  $stmt = $this->conn->prepare($query);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  return $result['roomID'] ?? null;
}


public function getNewFeedbacks() {
  // Count feedbacks provided in the last 30 days where feedback is 1
  $query = "
      SELECT COUNT(*) AS newFeedbacks 
      FROM bookings 
      WHERE feedback = 1 
      AND bookingTime >= NOW() - INTERVAL 30 DAY
  ";
  $stmt = $this->conn->query($query);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  return $result ? $result['newFeedbacks'] : 0;  // Return 0 if no feedbacks found
}

  public function getPendingBookings()
  {
    $currentDateTime = (new DateTime())->format('Y-m-d H:i:s'); // Get current date and time
    $result = $this->getBookingsBy('status', 'pending');

    if ($result === Constants::NO_RECORDS) {
      return [];
    }

    // Filter the results to ensure startTime is in the future
    $pendingBookings = array_filter($result, function ($booking) use ($currentDateTime) {
      return strtotime($booking['startTime']) > strtotime($currentDateTime); // Ensure startTime is in the future
    });

    return $pendingBookings;
  }


  public function getOpenLabBookings($userID)
  {
    $currentDateTime = (new DateTime())->format('Y-m-d H:i:s'); // Get current date and time

    // SQL to get the bookings for user with status 'active' or 'rejected' and room in specified list
    $sql = "SELECT * FROM bookings WHERE userID = :userID AND roomID IN ('S40-1002', 'S40-2001') AND status IN ('active', 'rejected') ORDER BY startTime DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter the results to ensure endTime is in the future
    $openLabBookings = array_filter($result, function ($booking) use ($currentDateTime) {
      return strtotime($booking['endTime']) > strtotime($currentDateTime); // Ensure endTime is in the future
    });

    return $openLabBookings;
  }


  public function updateExpiredBookings()
{
    // Get the previous bookings by the user
    $previousBookings = $this->getAllBookings();

    // If there are no previous bookings, return a constant indicating no records
    if (empty($previousBookings)) {
        return Constants::NO_RECORDS;
    }

    // Iterate through each previous booking
    foreach ($previousBookings as $previousBooking) {
        // If the room is not 'S40-1002' or 'S40-2001' and the status is not 'rejected'
        if (!(($previousBooking['roomID'] === 'S40-1002' || $previousBooking['roomID'] === 'S40-2001') && $previousBooking['status'] === 'rejected')) {
            
            $currentDateTime = (new DateTime())->format('Y-m-d H:i:s');
            if (strtotime($previousBooking['endTime']) < strtotime($currentDateTime) && $previousBooking['status'] !== 'expired') {
                // Update status to 'expired'
                $update = ['status' => 'expired'];
                $condition = 'bookingID = ?';
                $crud = new Crud($this->conn);
                $result = $crud->update('bookings', $update, $condition, $previousBooking['bookingID']);
            }
        }
    }

    return Constants::SUCCESS;
}

}
