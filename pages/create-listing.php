<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in and can list items
if (!isLoggedIn() || !canListItems()) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);
    $price_per_day = (float)$_POST['price_per_day'];
    $location = trim($_POST['location']);
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($title) || empty($description) || empty($location) || $price_per_day <= 0) {
        $error = 'Please fill in all required fields with valid values';
    } else {
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_path = uploadImage($_FILES['image']);
            if (!$image_path) {
                $error = 'Failed to upload image. Please try again.';
            }
        }

        if (empty($error)) {
            // Create the listing
            if (createListing($user_id, $category_id, $title, $description, $price_per_day, $location, $image_path)) {
                $success = 'Listing created successfully!';
                // Redirect to dashboard after a short delay
                header('Refresh: 2; URL=index.php');
            } else {
                $error = 'Failed to create listing. Please try again.';
            }
        }
    }
}

// Redirect back to dashboard if not a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}
?>
