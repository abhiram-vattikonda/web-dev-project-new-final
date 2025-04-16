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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Listing - Rental Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <a href="home.php" class="logo">RentalPlatform</a>
            <div class="nav-links">
                <a href="home.php">Home</a>
                <a href="listings.php">Listings</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="index.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Create New Listing</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form action="create-listing.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" name="category_id" required>
                                    <?php
                                    $categories = getCategories();
                                    foreach ($categories as $category):
                                    ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">Price per Day ($)</label>
                                <input type="number" class="form-control" id="price" name="price_per_day" step="0.01" min="0" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="image">Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Create Listing</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>RentalPlatform is your one-stop destination for finding the perfect rental items in your area.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="listings.php">Listings</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <ul>
                    <li>Email: info@rentalplatform.com</li>
                    <li>Phone: (123) 456-7890</li>
                    <li>Address: 123 Rental St, City, Country</li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html> 