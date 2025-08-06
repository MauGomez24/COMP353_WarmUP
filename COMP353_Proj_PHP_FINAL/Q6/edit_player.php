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

$stmt = $pdo->prepare("
    SELECT tp.role, cm.first_name, cm.last_name
    FROM team_players tp
    JOIN club_members cm ON tp.cm_id = cm.cm_id
    WHERE tp.cm_id = ? AND tp.team_id = ?
");
$stmt->execute([$cm_id, $team_id]);
$player = $stmt->fetch();

if (!$player) {
    include 'header.php';
    echo "<p>Error: Player not found on this team.</p>";
    include 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRole = $_POST['role'];

    $updateStmt = $pdo->prepare("
        UPDATE team_players
        SET role = ?
        WHERE cm_id = ? AND team_id = ?
    ");
    $updateStmt->execute([$newRole, $cm_id, $team_id]);

    header("Location: generate_teams.php?team_id=$team_id");
    exit;
}

include 'header.php';

$roles = ['libero', 'setter', 'outside hitter', 'opposite hitter', 'middle blocker', 'defensive specialist'];
?>

<h2>Edit Role for <?= htmlspecialchars($player['first_name'] . ' ' . $player['last_name']) ?> (ID: <?= $cm_id ?>)</h2>

<form method="POST">
    <label for="role">Select New Role:</label><br>
    <select name="role" required>
        <?php foreach ($roles as $r): ?>
            <option value="<?= $r ?>" <?= $r === $player['role'] ? 'selected' : '' ?>>
                <?= ucfirst($r) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Update Role">
</form>

<a href="generate_teams.php?team_id=<?= $team_id ?>">← Back to Team View</a>
<?php include 'footer.php'; ?>
