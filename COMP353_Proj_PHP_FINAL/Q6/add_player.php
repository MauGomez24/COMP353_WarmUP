<?php
include 'db.php';

$team_id = $_GET['team_id'] ?? null;
if (!$team_id) {
    include 'header.php';
    echo "<p>Error: team_id not provided.</p>";
    include 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cm_id = $_POST['cm_id'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("
        SELECT s.date
        FROM teams t
        JOIN sessions s ON t.session_id = s.session_id
        WHERE t.team_id = ?
    ");
    $stmt->execute([$team_id]);
    $session_date = $stmt->fetchColumn();

    if ($session_date) {
        $conflictStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM team_players tp
            JOIN teams t ON tp.team_id = t.team_id
            JOIN sessions s ON t.session_id = s.session_id
            WHERE tp.cm_id = ? AND s.date = ?
        ");
        $conflictStmt->execute([$cm_id, $session_date]);
        $conflict = $conflictStmt->fetchColumn();

        if ($conflict > 0) {
            $message = "Conflict detected: This member is already assigned to a team on $session_date.";
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO team_players (cm_id, team_id, role)
                VALUES (?, ?, ?)
            ");
            $insertStmt->execute([$cm_id, $team_id, $role]);
            header("Location: generate_teams.php?team_id=$team_id");
            exit;
        }
    } else {
        $message = "Error: Invalid team ID or session not found.";
    }
}

include 'header.php';

$members = $pdo->query("SELECT cm_id, first_name, last_name FROM club_members")->fetchAll();
$roles = ['libero', 'setter', 'outside hitter', 'opposite hitter', 'middle blocker', 'defensive specialist'];
?>

<h2>Add Member to Team (ID: <?= $team_id ?>)</h2>

<form method="POST">
    <label for="cm_id">Select Club Member:</label><br>
    <select name="cm_id" required>
        <?php foreach ($members as $member): ?>
            <option value="<?= $member['cm_id'] ?>">
                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?> (ID: <?= $member['cm_id'] ?>)
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="role">Select Role:</label><br>
    <select name="role" required>
        <?php foreach ($roles as $r): ?>
            <option value="<?= $r ?>"><?= ucfirst($r) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Assign to Team">
</form>

<?php if (isset($message)): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<a href="generate_teams.php?team_id=<?= $team_id ?>">← Back to Team View</a>
<?php include 'footer.php'; ?>
