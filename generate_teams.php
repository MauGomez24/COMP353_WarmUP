<?php
include 'header.php';
include 'db.php';

$teamInfo = null;
$players = [];

$team_id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['team_id'])) {
    $team_id = $_POST['team_id'];
} elseif (isset($_GET['team_id'])) {
    $team_id = $_GET['team_id'];
}

if ($team_id) {
    // Fetch team info
    $stmt = $pdo->prepare("
        SELECT t.team_id, t.name AS team_name, t.score, 
               s.date AS session_date, s.time AS session_time, s.type AS session_type,
               l.name AS location_name
        FROM teams t
        JOIN sessions s ON t.session_id = s.session_id
        JOIN locations l ON t.location_id = l.location_id
        WHERE t.team_id = ?
    ");
    $stmt->execute([$team_id]);
    $teamInfo = $stmt->fetch();

    // Fetch players on this team
    $playerStmt = $pdo->prepare("
        SELECT tp.cm_id, tp.role, cm.first_name, cm.last_name
        FROM team_players tp
        JOIN club_members cm ON tp.cm_id = cm.cm_id
        WHERE tp.team_id = ?
    ");
    $playerStmt->execute([$team_id]);
    $players = $playerStmt->fetchAll();
}

// Fetch all team options
$teams = $pdo->query("SELECT team_id, name FROM teams")->fetchAll();
?>

<h2>Select a Team</h2>

<form method="POST">
    <label for="team_id">Choose a team:</label>
    <select name="team_id" required>
        <?php foreach ($teams as $team): ?>
            <option value="<?= $team['team_id'] ?>" <?= $team_id == $team['team_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($team['name']) ?> (ID: <?= $team['team_id'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="View Info">
</form>

<?php if ($teamInfo): ?>
    <h3>Team Information</h3>
    <ul>
        <li><strong>ID:</strong> <?= $teamInfo['team_id'] ?></li>
        <li><strong>Name:</strong> <?= htmlspecialchars($teamInfo['team_name']) ?></li>
        <li><strong>Score:</strong> <?= $teamInfo['score'] ?></li>
        <li><strong>Session Date:</strong> <?= $teamInfo['session_date'] ?></li>
        <li><strong>Session Time:</strong> <?= $teamInfo['session_time'] ?></li>
        <li><strong>Session Type:</strong> <?= $teamInfo['session_type'] ?></li>
        <li><strong>Location:</strong> <?= htmlspecialchars($teamInfo['location_name']) ?></li>
    </ul>

    <h3>Players on This Team</h3>
    <?php if (count($players) > 0): ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $p): ?>
                    <tr>
                        <td><?= $p['cm_id'] ?></td>
                        <td><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td><?= $p['role'] ?></td>
                        <td>
                            <a href="edit_player.php?cm_id=<?= $p['cm_id'] ?>&team_id=<?= $teamInfo['team_id'] ?>">Edit</a> |
                            <a href="delete_player.php?cm_id=<?= $p['cm_id'] ?>&team_id=<?= $teamInfo['team_id'] ?>" onclick="return confirm('Are you sure you want to remove this player from the team?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No players assigned to this team yet.</p>
    <?php endif; ?>

    <br>
    <a href="add_player.php?team_id=<?= $teamInfo['team_id'] ?>">Add New Player to This Team</a>
<?php endif; ?>

<?php include 'footer.php'; ?>
