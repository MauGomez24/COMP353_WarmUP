<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing ID");
$fm_id = $_GET['id'];

try {
  $stmt = $pdo->prepare("DELETE FROM family_members WHERE fm_id = ?");
  $stmt->execute([$fm_id]);
  header("Location: family.php");
  exit;
} catch (PDOException $e) {
  echo "<p>Error deleting family member: " . $e->getMessage() . "</p>";
}
?>
