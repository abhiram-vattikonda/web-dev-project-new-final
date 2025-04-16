<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in and can list items
if (!isLoggedIn() || !canListItems()) {
    header('Location: login.php');
    exit();
}

// Check if listing ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$listing_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify that the listing belongs to the current user
$query = "SELECT id FROM listings WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $listing_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    // Listing doesn't exist or doesn't belong to the user
    header('Location: index.php');
    exit();
}

// Delete the listing
$query = "DELETE FROM listings WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $listing_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    // Success - redirect back to dashboard
    header('Location: index.php?success=Listing deleted successfully');
} else {
    // Error - redirect back to dashboard with error message
    header('Location: index.php?error=Failed to delete listing');
}
exit(); 