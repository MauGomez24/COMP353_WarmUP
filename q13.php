<?php
include 'header.php';
include 'db.php';

$query = "
    SELECT
        cm.cm_id,
        cm.first_name,
        cm.last_name,
        cm.age,
        cm.phone,
        cm.email,
        l.name AS location_name
    FROM club_members cm
    JOIN memberships m ON cm.cm_id = m.cm_id
    JOIN locations l ON cm.location_id = l.location_id
    WHERE cm.cm_id NOT IN (
        SELECT DISTINCT cm_id
        FROM team_players
    )
    AND m.is_active = 1
    ORDER BY l.name ASC, cm.age ASC
";

$stmt = $pdo->query($query);
$members = $stmt->fetchAll();
?>

<h2>Active Members Never Assigned to a Team Formation</h2>

<?php if (count($members) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Member ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= $m['cm_id'] ?></td>
                    <td><?= htmlspecialchars($m['first_name']) ?></td>
                    <td><?= htmlspecialchars($m['last_name']) ?></td>
                    <td><?= $m['age'] ?></td>
                    <td><?= $m['phone'] ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['location_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No active members found without team assignment.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
