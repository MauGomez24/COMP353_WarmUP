<?php
include 'header.php';
include 'db.php';

$query = "
    SELECT
        s.session_id,
        p.first_name AS coach_first_name,
        p.last_name AS coach_last_name,
        s.date AS session_date,
        s.time AS session_time,
        l.address AS session_address,
        s.type AS session_type,
        t.name AS team_name,
        CASE
            WHEN s.date <= CURDATE() THEN t.score
            ELSE NULL
        END AS team_score,
        cm.first_name AS player_first_name,
        cm.last_name AS player_last_name,
        tp.role AS player_role
    FROM sessions s
    JOIN locations l ON s.location_id = l.location_id
    JOIN teams t ON s.session_id = t.session_id
    JOIN personnel p ON t.coach_id = p.employee_id
    JOIN team_players tp ON t.team_id = tp.team_id
    JOIN club_members cm ON tp.cm_id = cm.cm_id
    WHERE s.location_id = 1 AND s.date >= '2023-01-01' AND s.date < '2024-01-01'
    ORDER BY s.date ASC, s.time ASC
";

$stmt = $pdo->query($query);
$records = $stmt->fetchAll();
?>

<h2>Team Formations at Location 1 (Jan 1 – Dec 31, 2023)</h2>

<?php if (count($records) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Coach Name</th>
                <th>Session Date</th>
                <th>Session Time</th>
                <th>Session Address</th>
                <th>Session Type</th>
                <th>Team Name</th>
                <th>Team Score</th>
                <th>Player First Name</th>
                <th>Player Last Name</th>
                <th>Player Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['session_id'] ?></td>
                    <td><?= htmlspecialchars($r['coach_first_name'] . ' ' . $r['coach_last_name']) ?></td>
                    <td><?= $r['session_date'] ?></td>
                    <td><?= $r['session_time'] ?></td>
                    <td><?= htmlspecialchars($r['session_address']) ?></td>
                    <td><?= ucfirst($r['session_type']) ?></td>
                    <td><?= htmlspecialchars($r['team_name']) ?></td>
                    <td><?= is_null($r['team_score']) ? 'Upcoming' : $r['team_score'] ?></td>
                    <td><?= htmlspecialchars($r['player_first_name']) ?></td>
                    <td><?= htmlspecialchars($r['player_last_name']) ?></td>
                    <td><?= $r['player_role'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No team formations found for the selected period and location.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
