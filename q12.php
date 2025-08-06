<?php
include 'header.php';
include 'db.php';

$query = "
    SELECT
        l.location_id,
        l.name AS location_name,
        COUNT(DISTINCT CASE WHEN s.type = 'train' THEN s.session_id END) AS total_training_sessions,
        COUNT(DISTINCT CASE WHEN s.type = 'train' THEN CONCAT(s.session_id, '_', tp.cm_id) END) AS total_training_players,
        COUNT(DISTINCT CASE WHEN s.type = 'game' THEN s.session_id END) AS total_game_sessions,
        COUNT(DISTINCT CASE WHEN s.type = 'game' THEN CONCAT(s.session_id, '_', tp.cm_id) END) AS total_game_players
    FROM sessions s
    JOIN locations l ON s.location_id = l.location_id
    JOIN teams t ON s.session_id = t.session_id
    JOIN team_players tp ON t.team_id = tp.team_id
    WHERE s.date BETWEEN '2023-09-01' AND '2023-10-31'
    GROUP BY l.location_id, l.name
    HAVING total_game_sessions >= 4
    ORDER BY total_game_sessions DESC
";

$stmt = $pdo->query($query);
$locations = $stmt->fetchAll();
?>

<h2>Team Formation Report (Sep 1 – Oct 31, 2023)</h2>

<?php if (count($locations) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Location ID</th>
                <th>Location Name</th>
                <th>Total Training Sessions</th>
                <th>Total Training Players</th>
                <th>Total Game Sessions</th>
                <th>Total Game Players</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $l): ?>
                <tr>
                    <td><?= $l['location_id'] ?></td>
                    <td><?= htmlspecialchars($l['location_name']) ?></td>
                    <td><?= $l['total_training_sessions'] ?></td>
                    <td><?= $l['total_training_players'] ?></td>
                    <td><?= $l['total_game_sessions'] ?></td>
                    <td><?= $l['total_game_players'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No qualifying location records found for the selected time period.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
