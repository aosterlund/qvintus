<?php
include_once '../includes/config.php';
include_once '../includes/class.book.php';

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $bookClass = new Book($pdo);

    // Get the search results from the database
    $results = $bookClass->searchBooks($query);

    if (empty($results)) {
        echo '<p class="text-center">No books found for "' . htmlspecialchars($query) . '".</p>';
    } else {
        echo '<div class="row">';
        foreach ($results as $book) {
            echo '<div class="col-md-4 mb-4">';
            echo '<div class="card">';
            echo '<img src="' . htmlspecialchars($book['img_url']) . '" class="card-img-top" alt="' . htmlspecialchars($book['book_title']) . '">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($book['book_title']) . '</h5>';
            echo '<p class="card-text"><strong>Price:</strong> $' . htmlspecialchars($book['books_price']) . '</p>';
            echo '<a href="singlebook.php?id=' . htmlspecialchars($book['book_id']) . '" class="btn btn-primary">View Details</a>';
            echo '</div></div></div>';
        }
        echo '</div>';
    }
} else {
    echo '<p class="text-center">Invalid request.</p>';
}
?>