<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        // In a real application, you would send an email here
        // For now, we'll just simulate success
        $success = 'Thank you for your message! We will get back to you soon.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Rental Platform</title>
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

    <!-- Contact Section -->
    <section class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4">Contact Us</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form id="contact-form" method="POST" action="">
                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="message">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="mb-4">Get in Touch</h2>
                        <div class="contact-info">
                            <div class="mb-4">
                                <h4>Address</h4>
                                <p>123 Rental St<br>City, State 12345<br>Country</p>
                            </div>

                            <div class="mb-4">
                                <h4>Contact Information</h4>
                                <p>
                                    <strong>Email:</strong> info@rentalplatform.com<br>
                                    <strong>Phone:</strong> (123) 456-7890<br>
                                    <strong>Fax:</strong> (123) 456-7891
                                </p>
                            </div>

                            <div class="mb-4">
                                <h4>Business Hours</h4>
                                <p>
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed
                                </p>
                            </div>

                            <div>
                                <h4>Follow Us</h4>
                                <div class="social-links">
                                    <a href="#" class="btn btn-outline-primary me-2">Facebook</a>
                                    <a href="#" class="btn btn-outline-info me-2">Twitter</a>
                                    <a href="#" class="btn btn-outline-danger me-2">Instagram</a>
                                    <a href="#" class="btn btn-outline-secondary">LinkedIn</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="container my-5">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center mb-4">Our Location</h3>
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.2155710122!2d-73.987844924164!3d40.757978971389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1681234567890!5m2!1sen!2sus" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
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