<?php
include 'db.php';

if (!isset($_GET['id'])) {
  die("Missing team ID");
}

$team_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM team_players WHERE team_id = ?");
$stmt->execute([$team_id]);
$in_use = $stmt->fetchColumn() > 0;

if ($in_use) {
  echo "<p>Error: Cannot delete team. It is in use in team_players.</p>";
  echo "<p><a href='teams.php'>Back to Teams</a></p>";
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM teams WHERE team_id = ?");
  $stmt->execute([$team_id]);
  header("Location: teams.php");
  exit;
} catch (PDOException $e) {
  echo "<p>Error deleting team: " . $e->getMessage() . "</p>";
}
?>
