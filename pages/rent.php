<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize input
    $listing_id = isset($_POST['listing_id']) ? (int)$_POST['listing_id'] : 0;
    $renter_id = isset($_POST['renter_id']) ? (int)$_POST['renter_id'] : $_SESSION['user_id'];
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0.0;

    // Validate
    if (!$listing_id || !$renter_id || !$start_date || !$end_date || $total_price <= 0) {
        header("Location: index.php?message=Invalid rental information.");
        exit;
    }

    // Check date logic
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    if ($start > $end) {
        header("Location: index.php?message=Start date must be before end date.");
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into rentals/booking table
        $insertQuery = "INSERT INTO bookings (listing_id, renter_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $status = "booked";
        $stmt->bind_param("iissds", $listing_id, $renter_id, $start_date, $end_date, $total_price, $status);
        $stmt->execute();

        // Update listing as rented
        $updateQuery = "UPDATE listings SET is_rented = 1, rented_by = ? WHERE id = ?";
        $stmt2 = $conn->prepare($updateQuery);
        $stmt2->bind_param("ii", $renter_id, $listing_id);
        $stmt2->execute();

        $conn->commit();

        // Redirect
        header("Location: index.php?message=Item rented successfully");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: index.php?message=Rental failed: " . $e->getMessage());
        exit;
    }

} else {
    header("Location: index.php?message=Invalid request method.");
    exit;
}
?>
