<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("Error: No location ID provided.");
}

$location_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM locations WHERE location_id = ?");
    $stmt->execute([$location_id]);
    header("Location: locations.php");
    exit;
} catch (PDOException $e) {
    die("Error deleting location: " . $e->getMessage());
}
?>
