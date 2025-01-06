<?php
// Ensure the Book.php file is included from the same directory
if (file_exists(__DIR__ . '/includes/book.php')) {
    include_once __DIR__ . '/includes/book.php';
} else {
    die('book.php file not found!');
}

include_once 'includes/header.php';

// Initialize the Book class, passing the PDO object (assumed to be already defined)
$bookClass = new Book($pdo);

// Initialize variable for the book
$book = null;

if (isset($_GET['id'])) {
    // Sanitize the book ID input to prevent any malicious input
    $bookId = intval($_GET['id']);
    
    // Fetch the current book by ID
    $book = $bookClass->getBookById($bookId); // Assuming getBookById() works as expected
}
?>

<div class="container my-5">
    <?php if (!empty($book)): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($book['book_title']); ?></h5>
                <p class="card-text"><strong>Description:</strong> <?= htmlspecialchars($book['book_desc']); ?></p>
                <p class="card-text"><strong>Pages:</strong> <?= htmlspecialchars($book['book_pages']); ?></p>
                <p class="card-text"><strong>Price:</strong> $<?= htmlspecialchars($book['books_price']); ?></p>
                <p class="card-text"><strong>Authors:</strong> <?= htmlspecialchars($book['book_author']); ?></p>
            </div>
        </div>

    <?php else: ?>
        <p>Book not found!</p>
    <?php endif; ?>

    
        <!-- Tillbaka (Back) Button -->
        <div class="mt-3">
            <a href="index.php" class="btn btn-primary">Tillbaka</a>
        </div>

</div>


<?php include_once 'includes/footer.php'; ?>
