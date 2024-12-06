<?php
date_default_timezone_set('Asia/Bahrain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/user-model.php';
require_once '../database/book-model.php';
require_once '../database/room-model.php';
require_once '../utils/helpers.php';

header('Content-Type: application/json');

// Instantiate models
$bookingModel = new BookModel($pdo, null, null, null, null, null, null);
$userModel = new UserModel($pdo);
$roomModel = new RoomModel($pdo);

// Fetch data
// Fetch data with proper validation
$bookingCount = $bookingModel->getTotalBookings();
$mostBookedRoom = $bookingModel->getMostBookedRoom();
$bookingStats = $bookingModel->getBookingsByMonth();
$departmentStats = $bookingModel->getBookingsByDepartment();
$totalUsers = $userModel->getTotalUsers();
$newFeedbacks = $bookingModel->getNewFeedbacks();

// Prepare fallback values
$bookingCount = is_numeric($bookingCount) ? $bookingCount : 0;
$mostBookedRoom = $mostBookedRoom ?? '...';
$bookingStats = !empty($bookingStats) ? $bookingStats : [];
$departmentStats = !empty($departmentStats) ? $departmentStats : [];
$totalUsers = is_numeric($totalUsers) ? $totalUsers : 0;
$newFeedbacks = is_numeric($newFeedbacks) ? $newFeedbacks : 0;

// Return JSON data
echo json_encode([
    'bookingCount' => $bookingCount,
    'mostBookedRoom' => $mostBookedRoom,
    'bookingStats' => $bookingStats,
    'departmentStats' => $departmentStats,
    'totalUsers' => $totalUsers,
    'newFeedbacks' => $newFeedbacks
]);

?>
