<?php
require_once 'config.php';

// Function to register a new user
function registerUser($username, $email, $password) {
    global $conn;
    
    // Check if username or email already exists
    $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0) {
        return "Username or email already exists";
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert new user
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating user");
        }
        
        $user_id = mysqli_insert_id($conn);
        
        // Insert default capabilities
        $query = "INSERT INTO user_capabilities (user_id) VALUES (?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error setting user capabilities");
        }
        
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return "Error: " . $e->getMessage();
    }
}

// Function to login user
function loginUser($username, $password) {
    global $conn;
    
    $query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)) {
        if(password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            return true;
        }
    }
    return false;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user can list items
function canListItems() {
    global $conn;
    if (!isLoggedIn()) return false;
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT can_list FROM user_capabilities WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)) {
        return (bool)$row['can_list'];
    }
    return false;
}

// Function to check if user can rent items
function canRentItems() {
    global $conn;
    if (!isLoggedIn()) return false;
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT can_rent FROM user_capabilities WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)) {
        return (bool)$row['can_rent'];
    }
    return false;
}

// Function to logout user
function logoutUser() {
    session_destroy();
    header("Location: " . BASE_URL . "/pages/login.php");
    exit();
} 