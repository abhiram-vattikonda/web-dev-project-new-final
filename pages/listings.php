<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Get filter parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$location = isset($_GET['location']) ? trim($_GET['location']) : null;
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

// Get listings based on filters
$listings = getListings($category_id, $location, $min_price, $max_price);
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings - Rental Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
        <a class="navbar-brand" href="listings.php">RentalPlatform</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="listings.php">Listings</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<!-- Filter Form -->
<div class="container my-4">
    <div class="card p-4">
        <h3>Filter Listings</h3>
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-3">
                    <label for="category">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="location">Location</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="min_price">Min Price</label>
                    <input type="number" name="min_price" class="form-control" value="<?= $min_price ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label for="max_price">Max Price</label>
                    <input type="number" name="max_price" class="form-control" value="<?= $max_price ?? '' ?>">
                </div>
            </div>
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="listings.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Listings -->
<div class="container my-4">
    <h2 class="mb-4">Available Listings</h2>
    <div class="row">
        <?php if (empty($listings)): ?>
            <div class="col-12">
                <div class="alert alert-info">No listings found.</div>
            </div>
        <?php else: ?>
            <?php foreach ($listings as $listing): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= $listing['image_path'] ? "../pages/uploads/" . htmlspecialchars($listing['image_path']) : "../assets/images/placeholder.jpg" ?>"
                             class="card-img-top" alt="<?= htmlspecialchars($listing['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($listing['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($listing['description']) ?></p>
                            <p><strong>Category:</strong> <?= htmlspecialchars($listing['category_name']) ?></p>
                            <p><strong>Price:</strong> $<?= number_format($listing['price_per_day'], 2) ?>/day</p>
                            <p><strong>Location:</strong> <?= htmlspecialchars($listing['location']) ?></p>
                            <p><strong>Owner:</strong> <?= htmlspecialchars($listing['owner_name']) ?></p>
                            <!-- <a href="listing-details.php?id=<?= $listing['id'] ?>" class="btn btn-outline-primary">View Details</a>
                            <?php if (isLoggedIn()): ?>
                                <!-- Rent Button -->
                                <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#rentModal<?= $listing['id'] ?>">Rent Now</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Rent Modal -->
                <div class="modal fade" id="rentModal<?= $listing['id'] ?>" tabindex="-1" aria-labelledby="rentModalLabel<?= $listing['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="rent.php" method="POST" class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Rent <?= htmlspecialchars($listing['title']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                        <input type="hidden" name="renter_id" value="<?= $_SESSION['user_id'] ?>">
                        <input type="hidden" id="price_per_day" value="<?= $listing['price_per_day'] ?>">

                        <div class="mb-3">
                          <label for="start_date" class="form-label">Start Date</label>
                          <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                          <label for="end_date" class="form-label">End Date</label>
                          <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                          <label for="total_price" class="form-label">Total Price</label>
                          <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="total_price" id="total_price" class="form-control" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm Rental</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </div>
                    </form>
                  </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white p-4 mt-5">
    <div class="row container mx-auto">
        <div class="col-md-4">
            <h5>About Us</h5>
            <p>RentalPlatform helps users rent and list items with ease.</p>
        </div>
        <div class="col-md-4">
            <h5>Quick Links</h5>
            <ul class="list-unstyled">
                <li><a href="home.php" class="text-white">Home</a></li>
                <li><a href="listings.php" class="text-white">Listings</a></li>
                <li><a href="about.php" class="text-white">About</a></li>
                <li><a href="contact.php" class="text-white">Contact</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <h5>Contact</h5>
            <ul class="list-unstyled">
                <li>Email: info@rentalplatform.com</li>
                <li>Phone: (123) 456-7890</li>
                <li>Address: 123 Rental St, City, Country</li>
            </ul>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to date inputs
    const startDateInputs = document.querySelectorAll('input[name="start_date"]');
    const endDateInputs = document.querySelectorAll('input[name="end_date"]');
    
    startDateInputs.forEach(input => {
        input.addEventListener('change', calculateTotalPrice);
    });
    
    endDateInputs.forEach(input => {
        input.addEventListener('change', calculateTotalPrice);
    });
    
    function calculateTotalPrice(event) {
        const modal = event.target.closest('.modal');
        const startDate = new Date(modal.querySelector('input[name="start_date"]').value);
        const endDate = new Date(modal.querySelector('input[name="end_date"]').value);
        const pricePerDay = parseFloat(modal.querySelector('input[id="price_per_day"]').value);
        const totalPriceInput = modal.querySelector('input[name="total_price"]');
        
        if (startDate && endDate && !isNaN(pricePerDay)) {
            if (endDate >= startDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include both start and end dates
                const totalPrice = diffDays * pricePerDay;
                totalPriceInput.value = totalPrice.toFixed(2);
            } else {
                totalPriceInput.value = '';
                alert('End date must be after start date');
            }
        }
    }
});
</script>

</body>
</html>
