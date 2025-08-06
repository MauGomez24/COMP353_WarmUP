<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing ID");
$cm_id = $_GET['id'];

try {
  $stmt = $pdo->prepare("DELETE FROM club_members WHERE cm_id = ?");
  $stmt->execute([$cm_id]);
  header("Location: members.php");
  exit;
} catch (PDOException $e) {
  echo "<p>Error deleting club member: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
