
<?php
include_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $genreName = $_POST['genreName'];

    try {
        $stmt = $pdo->prepare("INSERT INTO table_genres (genre_name) VALUES (:genre_name)");
        $stmt->bindParam(':genre_name', $genreName);
        $stmt->execute();
        $genreId = $pdo->lastInsertId();

        echo json_encode(['status' => 'success', 'genre_id' => $genreId]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>