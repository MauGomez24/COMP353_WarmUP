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
        fam_active_members AS (
            SELECT
                ml.cm_id,
                cm.fm_id,
                fm.first_name,
                fm.last_name,
                fm.phone,
                fm.ssn
            FROM membership_list ml
            INNER JOIN club_members cm ON ml.cm_id = cm.cm_id
            INNER JOIN family_members fm ON cm.fm_id = fm.fm_id
            WHERE ml.rn = 1 AND ml.is_active = 1
        ),
        fam_coaches AS (
            SELECT DISTINCT
                fam.first_name,
                fam.last_name,
                fam.phone
            FROM fam_active_members fam
            INNER JOIN personnel p ON fam.ssn = p.ssn
            INNER JOIN teams t ON t.coach_id = p.employee_id
        )
    SELECT * FROM fam_coaches
";

$stmt = $pdo->query($query);
$results = $stmt->fetchAll();
?>

<h2>Family Members Who Are Head Coaches for the Same Location</h2>

<?php if (count($results) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['first_name']) ?></td>
                    <td><?= htmlspecialchars($r['last_name']) ?></td>
                    <td><?= $r['phone'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No qualifying family members found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
