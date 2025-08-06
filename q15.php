<?php
include 'header.php';
include 'db.php';

$query = "
    WITH
        setters_id AS (
            SELECT cm_id
            FROM team_players
            GROUP BY cm_id
            HAVING 
                COUNT(*) >= 1
                AND SUM(role != 'setter') = 0
        ),
        setters_info AS (
            SELECT
                cm.cm_id,
                cm.first_name,
                cm.last_name,
                cm.age,
                cm.phone,
                cm.email,
                loc.name AS location_name
            FROM setters_id st
            INNER JOIN club_members cm ON st.cm_id = cm.cm_id
            INNER JOIN locations loc ON cm.location_id = loc.location_id
            JOIN memberships m ON cm.cm_id = m.cm_id
            WHERE m.is_active = 1
        )
    SELECT * FROM setters_info
    ORDER BY location_name ASC, cm_id ASC
";

$stmt = $pdo->query($query);
$members = $stmt->fetchAll();
?>

<h2>Active Members Assigned Only as Setters</h2>

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
    <p>No members found who have only been assigned as setters.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
