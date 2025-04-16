<?php
require_once 'config.php';

// Function to get all categories
function getCategories() {
    global $conn;
    $query = "SELECT * FROM categories";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to create a new listing
function createListing($user_id, $category_id, $title, $description, $price_per_day, $location, $image_path) {
    global $conn;
    
    $query = "INSERT INTO listings (user_id, category_id, title, description, price_per_day, location, image_path) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iissdss", $user_id, $category_id, $title, $description, $price_per_day, $location, $image_path);
    
    return mysqli_stmt_execute($stmt);
}

// Function to get listings with optional filters
function getListings($category_id = null, $location = null, $min_price = null, $max_price = null) {
    global $conn;
    
    $query = "SELECT l.*, c.name as category_name, u.username as owner_name 
              FROM listings l 
              JOIN categories c ON l.category_id = c.id 
              JOIN users u ON l.user_id = u.id 
              WHERE l.is_available = 1";
    
    $params = array();
    $types = "";
    
    if ($category_id) {
        $query .= " AND l.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }
    
    if ($location) {
        $query .= " AND l.location LIKE ?";
        $params[] = "%$location%";
        $types .= "s";
    }
    
    if ($min_price) {
        $query .= " AND l.price_per_day >= ?";
        $params[] = $min_price;
        $types .= "d";
    }
    
    if ($max_price) {
        $query .= " AND l.price_per_day <= ?";
        $params[] = $max_price;
        $types .= "d";
    }
    
    $query .= " ORDER BY l.created_at DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to create a booking
function createBooking($listing_id, $renter_id, $start_date, $end_date, $total_price) {
    global $conn;
    
    $query = "INSERT INTO bookings (listing_id, renter_id, start_date, end_date, total_price) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iissd", $listing_id, $renter_id, $start_date, $end_date, $total_price);
    
    return mysqli_stmt_execute($stmt);
}

// Function to get user's bookings
function getUserBookings($user_id, $role) {
    global $conn;
    
    if ($role === 'owner') {
        $query = "SELECT b.*, l.title, u.username as renter_name 
                  FROM bookings b 
                  JOIN listings l ON b.listing_id = l.id 
                  JOIN users u ON b.renter_id = u.id 
                  WHERE l.user_id = ?";
    } else {
        $query = "SELECT b.*, l.title, u.username as owner_name 
                  FROM bookings b 
                  JOIN listings l ON b.listing_id = l.id 
                  JOIN users u ON l.user_id = u.id 
                  WHERE b.renter_id = ?";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to handle image upload
function uploadImage($file) {
    $target_dir = UPLOAD_DIR;
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Check file size
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg") {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_filename;
    }
    return false;
} 