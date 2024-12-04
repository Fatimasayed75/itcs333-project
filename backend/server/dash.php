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
$bookingCount = $bookingModel->getTotalBookings();
$mostBookedRoom = $bookingModel->getMostBookedRoom();
$bookingStats = $bookingModel->getBookingsByMonth();
$departmentStats = $bookingModel->getBookingsByDepartment();
// Fetch total users count
$totalUsers = $userModel->getTotalUsers();

$newFeedbacks = $bookingModel->getNewFeedbacks();

// Error handling
if (!$bookingCount || !$mostBookedRoom || !$bookingStats || !$departmentStats || !$totalUsers) {
    echo json_encode(['error' => 'Database error or missing data']);
    exit();
}

// Return JSON data
$data = [
    'bookingCount' => $bookingCount,
    'mostBookedRoom' => $mostBookedRoom,
    'bookingStats' => $bookingStats,
    'departmentStats' => $departmentStats,
    'totalUsers' => $totalUsers,
    'newFeedbacks' => $newFeedbacks
];


echo json_encode($data);
?>
