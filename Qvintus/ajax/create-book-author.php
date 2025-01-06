<?php
include_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authorName = $_POST['authorName'];

    try {
        $stmt = $pdo->prepare("INSERT INTO table_authors (author_name) VALUES (:author_name)");
        $stmt->bindParam(':author_name', $authorName);
        $stmt->execute();
        $authorId = $pdo->lastInsertId();

        echo json_encode(['status' => 'success', 'author_id' => $authorId]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>