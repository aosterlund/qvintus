<?php

class Bookstore {
    private $pdo;
    private $errorState = 0;
    private $errorMessages = [];

    // Constructor: Connect to the database
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Clean input to prevent SQL injection
    private function cleanInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    // Create a new author
    public function createAuthor($authorName) {
        $stmt = $this->pdo->prepare("INSERT INTO table_authors (author_name) VALUES (:author_name)");
        $stmt->bindParam(':author_name', $authorName, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Edit an author
    public function editAuthor($authorId, $authorName) {
        $stmt = $this->pdo->prepare("UPDATE table_authors SET author_name = :author_name WHERE author_id = :author_id");
        $stmt->bindParam(':author_name', $authorName, PDO::PARAM_STR);
        $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Delete an author
    public function deleteAuthor($authorId) {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM books_authors WHERE author_id = :author_id");
            $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->pdo->prepare("DELETE FROM table_authors WHERE author_id = :author_id");
            $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
            $stmt->execute();
            $this->pdo->commit();
            return "Author deleted successfully.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->errorMessages[] = $e->getMessage();
            return "Failed to delete author.";
        }
    }

    // Create a new genre
    public function createGenre($genreName) {
        $stmt = $this->pdo->prepare("INSERT INTO table_genres (genre_name) VALUES (:genre_name)");
        $stmt->bindParam(':genre_name', $genreName, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Edit a genre
    public function editGenre($genreId, $genreName) {
        $stmt = $this->pdo->prepare("UPDATE table_genres SET genre_name = :genre_name WHERE genre_id = :genre_id");
        $stmt->bindParam(':genre_name', $genreName, PDO::PARAM_STR);
        $stmt->bindParam(':genre_id', $genreId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Delete a genre
    public function deleteGenre($genreId) {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM books_genres WHERE genre_id = :genre_id");
            $stmt->bindParam(':genre_id', $genreId, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->pdo->prepare("DELETE FROM table_genres WHERE genre_id = :genre_id");
            $stmt->bindParam(':genre_id', $genreId, PDO::PARAM_INT);
            $stmt->execute();
            $this->pdo->commit();
            return "Genre deleted successfully.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->errorMessages[] = $e->getMessage();
            return "Failed to delete genre.";
        }
    }

    // Create a new illustrator
    public function createIllustrator($illustratorName) {
        $stmt = $this->pdo->prepare("INSERT INTO table_illustrators (illustrator_name) VALUES (:illustrator_name)");
        $stmt->bindParam(':illustrator_name', $illustratorName, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Edit an illustrator
    public function editIllustrator($illustratorId, $illustratorName) {
        $stmt = $this->pdo->prepare("UPDATE table_illustrators SET illustrator_name = :illustrator_name WHERE illustrator_id = :illustrator_id");
        $stmt->bindParam(':illustrator_name', $illustratorName, PDO::PARAM_STR);
        $stmt->bindParam(':illustrator_id', $illustratorId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Delete an illustrator
    public function deleteIllustrator($illustratorId) {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM books_illustrators WHERE illustrator_id = :illustrator_id");
            $stmt->bindParam(':illustrator_id', $illustratorId, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->pdo->prepare("DELETE FROM table_illustrators WHERE illustrator_id = :illustrator_id");
            $stmt->bindParam(':illustrator_id', $illustratorId, PDO::PARAM_INT);
            $stmt->execute();
            $this->pdo->commit();
            return "Illustrator deleted successfully.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->errorMessages[] = $e->getMessage();
            return "Failed to delete illustrator.";
        }
    }

    // Create a new book
    public function createBook($bookData) {
        $stmt = $this->pdo->prepare("INSERT INTO table_books (book_title, book_desc, book_language, book_release_date, book_pages, books_price, book_series_fk, age_recommendation_fk, category_fk, publisher_fk, status_fk, img_url)
            VALUES (:book_title, :book_desc, :book_language, :book_release_date, :book_pages, :books_price, :book_series_fk, :age_recommendation_fk, :category_fk, :publisher_fk, :status_fk, :img_url)");
        $stmt->execute([
            ':book_title' => $this->cleanInput($bookData['book_title']),
            ':book_desc' => $this->cleanInput($bookData['book_desc']),
            ':book_language' => $this->cleanInput($bookData['book_language']),
            ':book_release_date' => $bookData['book_release_date'],
            ':book_pages' => $bookData['book_pages'],
            ':books_price' => $bookData['books_price'],
            ':book_series_fk' => $bookData['book_series_fk'],
            ':age_recommendation_fk' => $bookData['age_recommendation_fk'],
            ':category_fk' => $bookData['category_fk'],
            ':publisher_fk' => $bookData['publisher_fk'],
            ':status_fk' => $bookData['status_fk'],
            ':img_url' => $this->cleanInput($bookData['img_url'])
        ]);
        return $this->pdo->lastInsertId();
    }

    // Update a book
    public function updateBook($bookId, $bookData) {
        try {
            $stmt = $this->pdo->prepare("UPDATE table_books SET
                book_title = :book_title,
                book_desc = :book_desc,
                book_language = :book_language,
                book_release_date = :book_release_date,
                book_pages = :book_pages,
                books_price = :books_price,
                book_series_fk = :book_series_fk,
                age_recommendation_fk = :age_recommendation_fk,
                category_fk = :category_fk,
                publisher_fk = :publisher_fk,
                status_fk = :status_fk,
                img_url = :img_url
            WHERE book_id = :book_id");

            $stmt->execute([
                ':book_title' => $this->cleanInput($bookData['book_title']),
                ':book_desc' => $this->cleanInput($bookData['book_desc']),
                ':book_language' => $this->cleanInput($bookData['book_language']),
                ':book_release_date' => $bookData['book_release_date'],
                ':book_pages' => $bookData['book_pages'],
                ':books_price' => $bookData['books_price'],
                ':book_series_fk' => $bookData['book_series_fk'],
                ':age_recommendation_fk' => $bookData['age_recommendation_fk'],
                ':category_fk' => $bookData['category_fk'],
                ':publisher_fk' => $bookData['publisher_fk'],
                ':status_fk' => $bookData['status_fk'],
                ':img_url' => $this->cleanInput($bookData['img_url']),
                ':book_id' => $bookId
            ]);
            return true;
        } catch (PDOException $e) {
            $this->errorState = 1;
            $this->errorMessages[] = $e->getMessage();
            return false;
        }
    }

    // Delete a book
    public function deleteBook($bookId) {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM books_authors WHERE book_id = :book_id");
            $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM books_illustrators WHERE book_id = :book_id");
            $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM books_genres WHERE book_id = :book_id");
            $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM table_books WHERE book_id = :book_id");
            $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
            $stmt->execute();
            $this->pdo->commit();
            return "Book deleted successfully.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->errorMessages[] = $e->getMessage();
            return "Failed to delete book.";
        }
    }

    // Get error messages
    public function getErrorMessages() {
        return $this->errorMessages;
    }
}
