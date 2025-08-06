<?php
include 'header.php';
include 'db.php';

$query = "
    WITH
        membership_list AS (
            SELECT
                cm_id,
                is_active,
                ROW_NUMBER() OVER(
                    PARTITION BY cm_id
                    ORDER BY memb_year ASC
                ) AS rn
            FROM memberships
        ),
        active_members AS (
            SELECT cm_id
            FROM membership_list
            WHERE rn = 1 AND is_active = 1
        ),
        matches AS (
            SELECT
                t.team_id,
                t.session_id,
                ROW_NUMBER() OVER(
                    PARTITION BY t.session_id
                    ORDER BY t.score DESC
                ) AS rn
            FROM teams t
            INNER JOIN sessions s ON s.session_id = t.session_id
            WHERE s.type = 'game'
        ),
        team_results AS (
            SELECT
                team_id,
                SUM(CASE WHEN rn = 1 THEN 1 ELSE 0 END) AS wins,
                SUM(CASE WHEN rn = 2 THEN 1 ELSE 0 END) AS losses
            FROM matches
            GROUP BY team_id
        ),
        undefeated_members AS (
            SELECT tp.cm_id
            FROM team_results tr
            INNER JOIN team_players tp ON tr.team_id = tp.team_id
            INNER JOIN active_members am ON tp.cm_id = am.cm_id
            WHERE tr.losses = 0
        ),
        undefeated_members_info AS (
            SELECT DISTINCT
                cm.cm_id,
                cm.first_name,
                cm.last_name,
                cm.age,
                cm.phone,
                cm.email,
                loc.name AS location_name
            FROM undefeated_members um
            INNER JOIN club_members cm ON um.cm_id = cm.cm_id
            INNER JOIN locations loc ON cm.location_id = loc.location_id
        )
    SELECT * FROM undefeated_members_info
    ORDER BY location_name ASC, cm_id ASC
";

$stmt = $pdo->query($query);
$members = $stmt->fetchAll();
?>

<h2>Active Members Who Never Lost a Game They Played</h2>

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
    <p>No undefeated active members found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
