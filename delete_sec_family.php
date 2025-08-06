<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing ID");

$sfm_id = $_GET['id'];

try {
  $check = $pdo->prepare("SELECT COUNT(*) FROM family_club_relations WHERE sfm_id = ?");
  $check->execute([$sfm_id]);

  if ($check->fetchColumn() > 0) {
    echo "<p style='color:black;'>Cannot delete: This secondary family member is referenced in family_club_relations.</p>";
    exit;
  }

  $stmt = $pdo->prepare("DELETE FROM sec_fam_members WHERE sfm_id = ?");
  $stmt->execute([$sfm_id]);
  header("Location: sec_family.php");
  exit;
} catch (PDOException $e) {
  echo "<p>Error deleting secondary family member: " . $e->getMessage() . "</p>";
}
?>
