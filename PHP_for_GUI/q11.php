<?php
include 'header.php';
include 'db.php';

$query = "
    SELECT
        cm.cm_id,
        cm.first_name,
        cm.last_name
    FROM
        club_members cm
    JOIN memberships m ON cm.cm_id = m.cm_id
    WHERE m.is_active = 0
      AND cm.cm_id IN (
          SELECT cm_id
          FROM payments
          GROUP BY cm_id
          HAVING YEAR(CURDATE()) - MIN(memb_year) >= 2
      )
      AND cm.cm_id IN (
          SELECT tp.cm_id
          FROM team_players tp
          JOIN teams t ON tp.team_id = t.team_id
          GROUP BY tp.cm_id
          HAVING COUNT(DISTINCT t.location_id) >= 2
      )
    ORDER BY cm.cm_id
";

$stmt = $pdo->query($query);
$members = $stmt->fetchAll();
?>

<h2>Inactive Club Members (2+ Locations, 2+ Years Membership)</h2>

<?php if (count($members) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Club Member ID</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= $m['cm_id'] ?></td>
                    <td><?= htmlspecialchars($m['first_name']) ?></td>
                    <td><?= htmlspecialchars($m['last_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No inactive long-term multi-location club members found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
