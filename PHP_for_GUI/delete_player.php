<?php
include 'db.php';

$cm_id = $_GET['cm_id'] ?? null;
$team_id = $_GET['team_id'] ?? null;

if (!$cm_id || !$team_id) {
    include 'header.php';
    echo "<p>Error: Missing cm_id or team_id.</p>";
    include 'footer.php';
    exit;
}

// Perform delete
$stmt = $pdo->prepare("DELETE FROM team_players WHERE cm_id = ? AND team_id = ?");
$stmt->execute([$cm_id, $team_id]);

// Redirect to team page
header("Location: generate_teams.php?team_id=$team_id");
exit;
