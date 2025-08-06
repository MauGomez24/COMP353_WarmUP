<?php
include 'header.php';
include 'db.php';

$query = "
    SELECT
        l.location_id,
        l.address,
        pc.city,
        pc.province,
        l.postal_code,
        l.phone,
        l.web_address,
        CASE WHEN l.is_head = 1 THEN 'Head' ELSE 'Branch' END AS type,
        l.max_capacity,
        (SELECT CONCAT(p.first_name, ' ', p.last_name)
         FROM personnel p
         WHERE p.location_id = l.location_id AND p.role = 'general manager'
         LIMIT 1) AS general_manager_name,
        (SELECT COUNT(*)
         FROM club_members cm
         WHERE cm.location_id = l.location_id AND cm.is_minor = 1) AS minor_members,
        (SELECT COUNT(*)
         FROM club_members cm
         WHERE cm.location_id = l.location_id AND cm.is_minor = 0) AS major_members,
        (SELECT COUNT(*)
         FROM teams t
         WHERE t.location_id = l.location_id) AS number_of_teams
    FROM
        locations l
    JOIN
        postal_codes pc ON l.postal_code = pc.postal_code
    ORDER BY
        pc.province ASC,
        pc.city ASC
";

$stmt = $pdo->query($query);
$locations = $stmt->fetchAll();
?>

<h2>Full Location Details</h2>

<?php if (count($locations) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Location ID</th>
                <th>Address</th>
                <th>City</th>
                <th>Province</th>
                <th>Postal Code</th>
                <th>Phone</th>
                <th>Web Address</th>
                <th>Type</th>
                <th>Max Capacity</th>
                <th>General Manager</th>
                <th>Minor Members</th>
                <th>Major Members</th>
                <th>Teams</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($locations as $loc): ?>
                <tr>
                    <td><?= $loc['location_id'] ?></td>
                    <td><?= htmlspecialchars($loc['address']) ?></td>
                    <td><?= htmlspecialchars($loc['city']) ?></td>
                    <td><?= htmlspecialchars($loc['province']) ?></td>
                    <td><?= $loc['postal_code'] ?></td>
                    <td><?= $loc['phone'] ?></td>
                    <td><?= htmlspecialchars($loc['web_address']) ?></td>
                    <td><?= $loc['type'] ?></td>
                    <td><?= $loc['max_capacity'] ?></td>
                    <td><?= htmlspecialchars($loc['general_manager_name']) ?></td>
                    <td><?= $loc['minor_members'] ?></td>
                    <td><?= $loc['major_members'] ?></td>
                    <td><?= $loc['number_of_teams'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No location data found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>

