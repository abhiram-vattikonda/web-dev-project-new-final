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
$listing = null;

// Check if listing ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$listing_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Get listing details
$query = "SELECT * FROM listings WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $listing_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit();
}

$listing = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $location = trim($_POST['location']);
    $category_id = (int)$_POST['category_id'];
    $availability = trim($_POST['availability']);

    // Validate input
    if (empty($title) || empty($description) || empty($price) || empty($location) || empty($category_id)) {
        $error = 'All fields are required';
    } else {
        // Update listing
        $query = "UPDATE listings SET 
            title = ?, 
            description = ?, 
            price = ?, 
            location = ?, 
            category_id = ?, 
            availability = ?,
            updated_at = NOW()
            WHERE id = ? AND user_id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssdsisii", 
            $title, $description, $price, $location, $category_id, $availability, $listing_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Listing updated successfully';
            // Refresh listing data
            $query = "SELECT * FROM listings WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ii", $listing_id, $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $listing = mysqli_fetch_assoc($result);
        } else {
            $error = 'Failed to update listing';
        }
    }
}

// Get categories for dropdown
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0">Edit Listing</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                    value="<?php echo htmlspecialchars($listing['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                    rows="4" required><?php echo htmlspecialchars($listing['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price (per day)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                        value="<?php echo $listing['price']; ?>" step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="<?php echo htmlspecialchars($listing['location']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                            <?php echo $category['id'] == $listing['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="availability" class="form-label">Availability</label>
                                <input type="text" class="form-control" id="availability" name="availability" 
                                    value="<?php echo htmlspecialchars($listing['availability']); ?>" 
                                    placeholder="e.g., Available weekdays, weekends only">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Listing</button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 