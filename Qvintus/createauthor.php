<?php
// Include necessary files
include_once 'includes/header.php';
include_once 'includes/class.book.php';










$book = new Book($pdo);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the author name from the form
    $authorName = $_POST['author_name'];

    // Validate the author name
    if (!empty($authorName)) {
        // Call the createAuthor method from the Book class
        if ($book->createAuthor($authorName)) {
            echo '<div class="alert alert-success">Author created successfully.</div>';
        } else {
            echo '<div class="alert alert-danger">Failed to create author.</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Please enter a valid author name.</div>';
    }

    // Button to go back to the book management page
    echo '<div class="text-center">
            <a href="book-management.php" class="btn btn-primary">Go to Book Management</a>
          </div>';
}
?>

<!-- Author Creation Form -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Author</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="author_name" class="form-label">Author Name</label>
            <input type="text" class="form-control" id="author_name" name="author_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Author</button>
    </form>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
