<?php
// Start the session
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not logged in as admin, redirect to login page
    header("Location: login.php");
    exit;
}

// Include the header
include_once 'includes/header.php';
?>

<div class="container text-center my-5">
    <div class="row justify-content-center">
        <!-- First Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Add Books</h5>
                    <a href="book-management.php" class="btn btn-primary">Go to Book Management</a>
                </div>
            </div>
        </div>

        <!-- Second Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Add Genre</h5>
                    <a href="creategenre.php" class="btn btn-primary">Go to Create Genre</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
