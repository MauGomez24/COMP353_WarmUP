<?php
include 'header.php';
include 'db.php';

$query = "
    WITH
        minor_members AS (
            SELECT
                fm_id,
                COUNT(cm_id) AS num_minors
            FROM club_members
            WHERE is_minor = 1
            GROUP BY fm_id
        ),
        volunteers AS (
            SELECT
                p.first_name,
                p.last_name,
                mm.num_minors,
                p.phone,
                p.email,
                loc.name AS location_name,
                p.role
            FROM family_members fm
            INNER JOIN minor_members mm ON fm.fm_id = mm.fm_id
            INNER JOIN personnel p ON fm.ssn = p.ssn
            INNER JOIN locations loc ON loc.location_id = p.location_id
            WHERE p.mandate = 'volunteer'
        )
    SELECT * FROM volunteers
    ORDER BY location_name ASC, role ASC, first_name ASC, last_name ASC
";

$stmt = $pdo->query($query);
$volunteers = $stmt->fetchAll();
?>

<h2>Volunteer Personnel Who Are Family of Minor Club Members</h2>

<?php if (count($volunteers) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th># Minor Club Members</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Location</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($volunteers as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['first_name']) ?></td>
                    <td><?= htmlspecialchars($v['last_name']) ?></td>
                    <td><?= $v['num_minors'] ?></td>
                    <td><?= $v['phone'] ?></td>
                    <td><?= htmlspecialchars($v['email']) ?></td>
                    <td><?= htmlspecialchars($v['location_name']) ?></td>
                    <td><?= htmlspecialchars($v['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No qualifying volunteer personnel found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
