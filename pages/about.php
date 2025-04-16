<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Rental Platform</title>
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

    <!-- About Section -->
    <section class="container my-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center mb-4">About RentalPlatform</h1>
                        
                        <div class="about-section mb-5">
                            <h2>Our Mission</h2>
                            <p>RentalPlatform is dedicated to connecting people who need temporary access to items with those who have items to share. Our mission is to make renting items as easy and convenient as possible, while promoting sustainable consumption and community building.</p>
                        </div>

                        <div class="about-section mb-5">
                            <h2>What We Offer</h2>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h3>Houses</h3>
                                            <p>Find the perfect temporary accommodation for your needs, whether it's a vacation rental or a short-term stay.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h3>Cars</h3>
                                            <p>Rent vehicles for your transportation needs, from daily commutes to special occasions.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h3>Tools</h3>
                                            <p>Access a wide range of tools and equipment for your projects without the need to purchase them.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="about-section mb-5">
                            <h2>How It Works</h2>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h3>1. Register</h3>
                                            <p>Create an account as either a renter or an owner to get started.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h3>2. List or Browse</h3>
                                            <p>Owners can list their items, while renters can browse available listings.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h3>3. Connect</h3>
                                            <p>Renters can request to book items, and owners can accept or decline requests.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="about-section mb-5">
                            <h2>Our Values</h2>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <h4>Trust & Safety</h4>
                                    <p>We prioritize the safety and security of our users through verification processes and secure payment systems.</p>
                                </li>
                                <li class="mb-3">
                                    <h4>Sustainability</h4>
                                    <p>By promoting sharing and reuse, we contribute to reducing waste and environmental impact.</p>
                                </li>
                                <li class="mb-3">
                                    <h4>Community</h4>
                                    <p>We believe in building strong communities through shared resources and positive interactions.</p>
                                </li>
                                <li class="mb-3">
                                    <h4>Convenience</h4>
                                    <p>Our platform is designed to make the rental process as smooth and hassle-free as possible.</p>
                                </li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <a href="register.php" class="btn btn-primary btn-lg">Join Us Today</a>
                        </div>
                    </div>
                </div>
            </div>
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