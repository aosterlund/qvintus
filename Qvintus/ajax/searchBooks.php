<?php
include_once '../includes/config.php';
include_once '../includes/class.book.php';

header('Content-Type: application/json');

// Initialize the query variable from GET request
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

// Only perform search if the query is not empty
if ($query !== '') {
    try {
        $book = new Book($pdo);
        
        // Fetch the exact search results based on title or description
        $results = $book->searchBooks($query);

        // Fetch similar books based on the query (for recommendations)
        $similarBooks = $book->getSimilarBooks($query);

        // Return both the search results and similar books in the response
        echo json_encode([
            'results' => $results, 
            'similarBooks' => $similarBooks
        ]);
    } catch (PDOException $e) {
        // Handle error and return error message as JSON
        echo json_encode(["error" => "An error occurred while processing your request."]);
    }
} else {
    // Return an error if query is empty
    echo json_encode([]);
}
?>
