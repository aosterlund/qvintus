<?php
// Include necessary files for functions, header, and database connection
include_once 'includes/functions.php';
include_once 'includes/header.php';
include_once 'includes/Book.php'; // Include the Book class

// Ensure the session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Database connection details
$host = 'localhost';
$db = '2024_qvintus';  // Your database name
$user = 'root';        // Database username
$pass = '';            // Database password (empty for XAMPP by default)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize the Book class
$book = new Book($pdo);

// Handle POST request to add a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_title'], $_POST['book_author'], $_POST['books_price'], $_POST['book_desc'])) {
        $title = $_POST['book_title'];
        $author = $_POST['book_author'];
        $price = $_POST['books_price'];
        $description = $_POST['book_desc'];

        try {
            // Insert the new book into the database
            $stmt = $pdo->prepare("INSERT INTO books (book_title, book_author, books_price, book_desc) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $author, $price, $description]);

            // Redirect to book-management.php after adding a book
            header("Location: book-management.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error adding the book: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!-- HTML Form to Add a New Book -->
<div class="container my-5">
    <h1 class="mb-4">Lägg till ny bok</h1>

    <!-- Display Errors -->
    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="book_title" class="form-label">Bok Title</label>
            <input type="text" class="form-control" id="book_title" name="book_title" required>
        </div>
        <div class="mb-3">
            <label for="book_author" class="form-label">Bok Författare</label>
            <input type="text" class="form-control" id="book_author" name="book_author" required>
        </div>
        <div class="mb-3">
            <label for="books_price" class="form-label">Pris</label>
            <input type="number" class="form-control" id="books_price" name="books_price" required>
        </div>
        <div class="mb-3">
        <label for="books_price" class="form-label">Sidor</label>
        <input type="number" class="form-control" id="books_pages" name="books_pages" required>
        </div>
        <div class="mb-3">
            <label for="book_desc" class="form-label">Beskrivning</label>
            <textarea class="form-control" id="book_desc" name="book_desc" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Lägg till bok</button>
    </form>
    <div class="mt-3">
    <a href="index.php" class="btn btn-primary">Tillbaka</a>
</div>
</div>

<?php
include_once 'includes/footer.php';
?>
