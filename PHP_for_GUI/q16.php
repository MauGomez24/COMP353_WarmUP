<?php
include 'header.php';
include 'db.php';

$query = "
    WITH
        setters AS (SELECT cm_id FROM team_players WHERE role = 'setter'),
        liberos AS (SELECT cm_id FROM team_players WHERE role = 'libero'),
        out_hit AS (SELECT cm_id FROM team_players WHERE role = 'outside hitter'),
        opp_hit AS (SELECT cm_id FROM team_players WHERE role = 'opposite hitter'),
        mid_block AS (SELECT cm_id FROM team_players WHERE role = 'middle blocker'),
        def_spec AS (SELECT cm_id FROM team_players WHERE role = 'defensive specialist'),
        all_roles AS (
            SELECT
                s.cm_id,
                cm.first_name,
                cm.last_name,
                cm.age,
                cm.phone,
                cm.email,
                loc.name AS location_name
            FROM setters s
            INNER JOIN liberos l ON s.cm_id = l.cm_id
            INNER JOIN out_hit ot ON s.cm_id = ot.cm_id
            INNER JOIN opp_hit op ON s.cm_id = op.cm_id
            INNER JOIN mid_block mb ON s.cm_id = mb.cm_id
            INNER JOIN def_spec ds ON s.cm_id = ds.cm_id
            INNER JOIN club_members cm ON s.cm_id = cm.cm_id
            INNER JOIN memberships m ON cm.cm_id = m.cm_id AND m.is_active = 1
            INNER JOIN locations loc ON cm.location_id = loc.location_id
        )
    SELECT * FROM all_roles
    ORDER BY location_name ASC, cm_id ASC
";

$stmt = $pdo->query($query);
$members = $stmt->fetchAll();
?>

<h2>Active Members Assigned to All Six Roles</h2>

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
    <p>No members found who have been assigned to all six roles.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
