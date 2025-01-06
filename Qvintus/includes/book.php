<?php
class Book {
    private $pdo;

    // Constructor accepts the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Method to create a new author
    public function createAuthor($authorName) {
        try {
            // Prepare SQL query to insert author
            $sql = "INSERT INTO authors (name) VALUES (:author_name)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':author_name', $authorName);

            // Execute the query and check if successful
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // In case of error, display the error message
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Method to fetch all books from the database
    public function getAllBooks() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM books");  // Query to fetch all books
            return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return all books as an associative array
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Method to fetch books based on a specific status (e.g., rare, popular)
    public function getBooksByStatus($status) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE book_status = :status");
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Method to search books by title or description (only one version)
    public function searchBooks($query) {
        // SQL query to search for books based on title or author
        $sql = "SELECT * FROM books WHERE book_title LIKE :query OR book_author LIKE :query";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookById($bookId) {
    $sql = 'SELECT * FROM books WHERE book_id = :book_id LIMIT 1'; // Fetch single book
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->execute();

    // Check if a result is returned
    if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch and return the data as an associative array
    } else {
        return null; // No result found
    }
}


    // Method to fetch popular genres
    public function getPopularGenres() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM genres ORDER BY genre_name ASC LIMIT 5");  // Example query for popular genres
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    // Method to fetch customer reviews
    public function fetchCustomerReviews() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM reviews ORDER BY review_rating DESC");  // Example query for reviews
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
}
?>
