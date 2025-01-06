<?php
include_once 'includes/functions.php';
include_once 'includes/header.php';
include_once 'includes/Book.php'; // Include the Book class

// Database connection
$host = 'localhost';
$db = '2024_qvintus';  // Database name
$user = 'root';        // MySQL username
$pass = '';            // MySQL password (empty in XAMPP by default)

try {
    // Establish a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Anslutning misslyckades: " . $e->getMessage();
    exit;
}

// Initialize the Book class
$book = new Book($pdo);

// Get the search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';  // Trim to remove spaces

// Fetch books based on the query or get empty array if no query is entered
$allBooks = !empty($query) ? $book->searchBooks($query) : [];
$rareBooks = $book->getBooksByStatus(4); // Status = 4 for rare
$popularBooks = $book->getBooksByStatus(5); // Status = 5 for popular

// Fetch genres and reviews
$popularGenres = $book->getPopularGenres();
$reviews = $book->fetchCustomerReviews();
?>

<!-- Hero Section with Search Bar -->
<div id="hero" class="text-center py-5 bg-light">
    <div class="container">
        <h1 class="my-5">Vad letar du efter?</h1>
        <div class="search-container mx-auto mb-4" style="max-width: 600px;">
            <!-- Updated Search Box -->
            <form action="index.php" method="get">
                <input type="text" class="form-control form-control-lg" placeholder="Sök efter bok..." name="q" id="searchInput" value="<?= htmlspecialchars($query) ?>">
                <button type="submit" class="btn btn-primary btn-lg mt-3">Sök</button>
            </form>
        </div>
    </div>
</div>

<!-- Search Results Section -->
<?php if (!empty($query)): ?> <!-- Only display if query is not empty -->
<div id="search-results" class="container my-5">
    <h2 class="h5 text-center my-4">Sökresultat för: "<?= htmlspecialchars($query) ?>"</h2>
    <div class="row g-3 justify-content-center">
        <?php
        if (!empty($allBooks)) {
            foreach ($allBooks as $bookItem) {
                echo '<div class="col-12 col-md-4 col-lg-2 mb-4">';
                echo '<div class="card text-center border-0 shadow-sm">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($bookItem['book_title']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($bookItem['book_author']) . '</p>';
                echo '<a href="singlebook.php?id=' . htmlspecialchars($bookItem['book_id']) . '" class="btn btn-primary btn-sm">Visa detaljer</a>';
                echo '</div></div></div>';
            }
        } else {
            echo '<p class="text-center">Inga böcker matchar din sökning.</p>';
        }
        ?>
    </div>
</div>
<?php endif; ?> <!-- End condition for search query -->

<!-- Genres Section -->
<div id="genres-section" class="container my-5">
    <h2 class="h5 text-center my-4">Sällsynta och värdefulla böcker:</h2>
    <div class="row text-center g-3 justify-content-center">
        <?php
        if (!empty($rareBooks)) {
            foreach ($rareBooks as $bookItem) {
                echo '<div class="col-6 col-md-4 col-lg-2 mb-4">';
                echo '<div class="card text-center border-0 shadow-sm">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($bookItem['book_title']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($bookItem['book_author']) . '</p>';
                echo '<a href="singlebook.php?id=' . htmlspecialchars($bookItem['book_id']) . '" class="btn btn-primary btn-sm">Visa detaljer</a>';
                echo '</div></div></div>';
            }
        } else {
            echo '<p class="text-center">Inga sällsynta böcker hittades.</p>';
        }
        ?>
    </div>
</div>

<!-- Popular Books Section -->
<div id="popular-section" class="container my-5">
    <h2 class="h5 text-center my-4">Populärt just nu:</h2>
    <div class="row g-3 justify-content-center">
        <?php
        if (!empty($popularBooks)) {
            foreach ($popularBooks as $bookItem) {
                echo '<div class="col-md-2">';
                echo '<div class="card my-2 shadow-sm border-0">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($bookItem['book_title']) . '</h5>';
                echo '<a href="singlebook.php?id=' . htmlspecialchars($bookItem['book_id']) . '" class="btn btn-primary btn-sm">Visa detaljer</a>';
                echo '</div></div></div>';
            }
        } else {
            echo '<p class="text-center">Inga populära böcker hittades.</p>';
        }
        ?>
    </div>
</div>

<!-- Contact Section -->
<div id="contact-section" class="container text-center my-5">
    <h2>Hittar du inte det du söker?</h2>
    <p>Inga problem, vi ordnar de flesta önskemål, stora som små..</p>
    <a href="contact.php" class="btn btn-primary btn-lg">Gör ett önskemål</a>
</div>

<!-- About Section -->
<div id="about-section" class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <h2>Hälsningar</h2>
            <p>Välkommen till Qvintus! En skattkammare för bokälskare, samlare och nyfikna. Upptäck, utforska och fördjupa dig i en värld av sällsynta och äldre böcker. Oavsett om du är ute efter ett unikt fynd eller bara vill bläddra, så har vi något speciellt för dig.</p>
        </div>
        <div class="col-md-6 d-flex justify-content-center align-items-center">
            <img src="images/54f2f0f2-7721-4964-b68d-fba0c454730e.jpg" alt="Om oss" class="img-fluid">
        </div>
    </div>
</div>

<!-- Customer Reviews Section -->
<div id="customer-section" class="container text-center my-5">
    <h2 class="h5 my-4">Kundberättelser</h2>
    <?php if (!empty($reviews)): ?>
        <div class="row text-center my-5">
            <?php foreach ($reviews as $review): ?>
                <div class="col-12 col-md-4 mb-4">
                    <div class="card text-center h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($review['review_title']); ?></h5>
                            <p class="card-text">"<?= htmlspecialchars($review['review_desc']); ?>"</p>
                            <p class="card-text"><strong>Betyg:</strong> <?= htmlspecialchars($review['review_rating']); ?>/10</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">Inga kundrecensioner hittades.</p>
    <?php endif; ?>
</div>
 

<!-- Footer -->
<?php
include_once 'includes/footer.php';
?>
