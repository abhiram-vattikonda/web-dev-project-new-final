<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's listings if they can list items
$user_listings = [];
if (canListItems()) {
    $query = "SELECT l.*, c.name as category_name 
              FROM listings l 
              JOIN categories c ON l.category_id = c.id 
              WHERE l.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_listings = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get user's bookings
$role = canListItems() ? 'owner' : 'renter';
$my_rented_items = getUserBookings($user_id, $role);
// Get items that the user has rented (i.e., confirmed rentals)
$my_rented_items = [];
$query = "SELECT l.*, r.start_date, r.end_date, r.total_price,r.status
          FROM bookings r 
          JOIN listings l ON r.listing_id = l.id 
          WHERE r.renter_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$my_rented_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Rental Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <a href="listings.php" class="logo">RentalEase</a>
            <div class="nav-links">
                <a href="listings.php">Listings</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="index.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <!-- Dashboard Content -->
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Dashboard</h3>
            <ul>
                <li><a href="#profile">Profile</a></li>
                <?php if (canListItems()): ?>
                    <li><a href="#listings">My Listings</a></li>
                    <li><a href="#new-listing">Create New Listing</a></li>
                <?php endif; ?>
                <?php if (canRentItems()): ?>
                    <li><a href="#bookings">My Bookings</a></li>
                <?php endif; ?>
                <li><a href="#messages">Messages</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Profile Section -->
            <section id="profile" class="mb-5">
                <h2>Profile</h2>
                <div class="card">
                    <div class="card-body">
                        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                        <p>Capabilities:</p>
                        <ul>
                            <?php if (canListItems()): ?>
                                <li>You can list items for rent</li>
                            <?php endif; ?>
                            <?php if (canRentItems()): ?>
                                <li>You can rent items</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </section>

            <?php if (canListItems()): ?>
                <!-- Listings Section -->
                <section id="listings" class="mb-5">
                    <h2>My Listings</h2>
                    <?php if (empty($user_listings)): ?>
                        <div class="alert alert-info">You haven't created any listings yet.</div>
                    <?php else: ?>
                        <div class="grid">
                            <?php foreach ($user_listings as $listing): ?>
                                <div class="card">
                                    <?php if ($listing['image_path']): ?>
                                        <img src="../pages/uploads/<?php echo htmlspecialchars($listing['image_path']); ?>" class="card-img" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                                    <?php else: ?>
                                        <img src="../assets/images/placeholder.jpg" class="card-img" alt="No image available">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo htmlspecialchars($listing['title']); ?></h3>
                                        <p class="card-text"><?php echo htmlspecialchars($listing['description']); ?></p>
                                        <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($listing['category_name']); ?></p>
                                        <p class="card-text"><strong>Price:</strong> $<?php echo number_format($listing['price_per_day'], 2); ?>/day</p>
                                        <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($listing['location']); ?></p>
                                        <p class="card-text"><strong>Status:</strong> <?php echo $listing['is_available'] ? 'Available' : 'Not Available'; ?></p>
                                        <div class="btn-group">
                                            <a href="delete-listing.php?id=<?php echo $listing['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this listing?')">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- New Listing Section -->
                <section id="new-listing" class="mb-5">
                    <h2>Create New Listing</h2>
                    <div class="card">
                        <div class="card-body">
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
                                    <input type="number" class="form-control" id="price" name="price_per_day" step="0.01" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-primary">Create Listing</button>
                            </form>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (canRentItems()): ?>
                <!-- Bookings Section -->
                <section id="bookings" class="mb-5">
                    <h2>My Bookings</h2>
                    <?php if (empty($my_rented_items)): ?>
                        <div class="alert alert-info">You don't have any bookings yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($my_rented_items as $booking): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($booking['title']); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($booking['start_date'])); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($booking['end_date'])); ?></td>
                                            <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                                            <td><?php echo ucfirst($booking['status']); ?></td>
                                            <td>
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                    <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm">Cancel</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
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