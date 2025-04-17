<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize input
    $listing_id = isset($_POST['listing_id']) ? (int)$_POST['listing_id'] : 0;
    $renter_id = isset($_POST['renter_id']) ? (int)$_POST['renter_id'] : $_SESSION['user_id'];
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0.0;

    // Log received data
    error_log("Received booking data: listing_id=$listing_id, renter_id=$renter_id, start_date=$start_date, end_date=$end_date, total_price=$total_price");

    // Validate
    if (!$listing_id || !$renter_id || !$start_date || !$end_date || $total_price <= 0) {
        error_log("Validation failed: listing_id=$listing_id, renter_id=$renter_id, start_date=$start_date, end_date=$end_date, total_price=$total_price");
        header("Location: index.php?message=Invalid rental information.");
        exit;
    }

    // Check date logic
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    if ($start > $end) {
        error_log("Date validation failed: start_date=$start_date, end_date=$end_date");
        header("Location: index.php?message=Start date must be before end date.");
        exit;
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into rentals/booking table
        $insertQuery = "INSERT INTO bookings (listing_id, renter_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }
        
        $status = "Booked";
        if (!mysqli_stmt_bind_param($stmt, "iissds", $listing_id, $renter_id, $start_date, $end_date, $total_price, $status)) {
            throw new Exception("Bind param failed: " . mysqli_stmt_error($stmt));
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

        // Update listing as rented
        $updateQuery = "UPDATE listings SET is_available = 0 WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $updateQuery);
        if (!$stmt2) {
            throw new Exception("Prepare failed for update: " . mysqli_error($conn));
        }
        
        if (!mysqli_stmt_bind_param($stmt2, "i", $listing_id)) {
            throw new Exception("Bind param failed for update: " . mysqli_stmt_error($stmt2));
        }
        
        if (!mysqli_stmt_execute($stmt2)) {
            throw new Exception("Execute failed for update: " . mysqli_stmt_error($stmt2));
        }

        if (!mysqli_commit($conn)) {
            throw new Exception("Commit failed: " . mysqli_error($conn));
        }

        // Redirect
        header("Location: index.php?message=Item rented successfully");
        exit;

    } catch (Exception $e) {
        error_log("Error in booking process: " . $e->getMessage());
        mysqli_rollback($conn);
        header("Location: index.php?message=Rental failed: " . $e->getMessage());
        exit;
    }

} else {
    header("Location: index.php?message=Invalid request method.");
    exit;
}
?>
