<?php
include 'header.php';
include 'db.php';

$query = "
    WITH
        majors AS (
            SELECT cm_id, age
            FROM club_members
            WHERE is_minor = 0
        ),
        active_majors AS (
            SELECT
                mem.cm_id,
                maj.age,
                mem.is_active,
                MIN(mem.memb_year) OVER(PARTITION BY mem.cm_id) AS earliest_year,
                MAX(mem.memb_year) OVER(PARTITION BY mem.cm_id) AS latest_year,
                ROW_NUMBER() OVER(PARTITION BY mem.cm_id ORDER BY mem.memb_year DESC) AS rn
            FROM memberships mem
            INNER JOIN majors maj ON mem.cm_id = maj.cm_id
        ),
        age_calc AS (
            SELECT
                ac.cm_id,
                MIN(YEAR(p.payment_date)) OVER(PARTITION BY p.cm_id) as start_date
            FROM active_majors ac
            INNER JOIN payments p
                ON ac.cm_id = p.cm_id AND ac.earliest_year = YEAR(p.payment_date)
            WHERE ac.rn = 1 AND ac.is_active = 1
                AND (ac.age - (ac.latest_year - ac.earliest_year)) < 18
        ),
        final_info AS (
            SELECT
                cm.cm_id,
                cm.first_name,
                cm.last_name,
                ac.start_date,
                cm.age,
                cm.phone,
                cm.email,
                loc.name AS location_name
            FROM club_members cm
            INNER JOIN age_calc ac ON cm.cm_id = ac.cm_id
            INNER JOIN locations loc ON cm.location_id = loc.location_id
            GROUP BY
                cm.cm_id, cm.first_name, cm.last_name, ac.start_date,
                cm.age, cm.phone, cm.email, loc.name
        )
    SELECT * FROM final_info
    ORDER BY location_name ASC, age ASC
";

$stmt = $pdo->query($query);
$members = $stmt->fetchAll();
?>

<h2>Active Major Members Who Joined as Minors</h2>

<?php if (count($members) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Member ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date Joined</th>
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
                    <td><?= $m['start_date'] ?></td>
                    <td><?= $m['age'] ?></td>
                    <td><?= $m['phone'] ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['location_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No matching members found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
