<?php
include 'db.php';
if (!isset($_GET['id'])) die("Missing ID");

$employee_id = $_GET['id'];
try {
  $stmt = $pdo->prepare("DELETE FROM personnel WHERE employee_id = ?");
  $stmt->execute([$employee_id]);
  header("Location: personnel.php");
  exit;
} catch (PDOException $e) {
  echo "<p>Error deleting personnel: " . $e->getMessage() . "</p>";
}
?>
