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
    <!-- Header -->
    <header>
        <nav class="navbar">
            <a href="listings.php" class="logo">RentalPlatform</a>
            <div class="nav-links">
                <a href="listings.php">Listings</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isLoggedIn()): ?>
                    <a href="index.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Filters -->
    <section class="container my-4">
        <div class="card">
            <div class="card-body">
                <h3>Filter Listings</h3>
                <form id="filter-form" method="GET" action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="category-filter">Category</label>
                                <select class="form-control" id="category-filter" name="category">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="location-filter">Location</label>
                                <input type="text" class="form-control" id="location-filter" name="location" value="<?php echo htmlspecialchars($location ?? ''); ?>" placeholder="Enter location">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="min-price-filter">Min Price</label>
                                <input type="number" class="form-control" id="min-price-filter" name="min_price" value="<?php echo $min_price ?? ''; ?>" placeholder="Min price">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="max-price-filter">Max Price</label>
                                <input type="number" class="form-control" id="max-price-filter" name="max_price" value="<?php echo $max_price ?? ''; ?>" placeholder="Max price">
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="listings.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Listings -->
    <section class="container my-4">
        <h2 class="mb-4">Available Listings</h2>
        <div class="grid">
            <?php if (empty($listings)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No listings found matching your criteria.</div>
                </div>
            <?php else: ?>
                <?php foreach ($listings as $listing): ?>
                    <div class="card listing-card" 
                         data-category="<?php echo $listing['category_id']; ?>"
                         data-location="<?php echo htmlspecialchars($listing['location']); ?>"
                         data-price="<?php echo $listing['price_per_day']; ?>">
                        <?php if ($listing['image_path']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($listing['image_path']); ?>" class="card-img" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                        <?php else: ?>
                            <img src="../assets/images/placeholder.jpg" class="card-img" alt="No image available">
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($listing['title']); ?></h3>
                            <p class="card-text"><?php echo htmlspecialchars($listing['description']); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($listing['category_name']); ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo number_format($listing['price_per_day'], 2); ?>/day</p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($listing['location']); ?></p>
                            <p class="card-text"><strong>Owner:</strong> <?php echo htmlspecialchars($listing['owner_name']); ?></p>
                            <a href="listing-details.php?id=<?php echo $listing['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

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