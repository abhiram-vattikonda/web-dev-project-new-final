<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Logout the user
logoutUser();

// Redirect to listings page
header('Location: listings.php');
exit(); 