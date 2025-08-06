<?php
include 'header.php';
include 'db.php';

$query = "
    SELECT
        fcr.fm_id,
        sfm.first_name AS sfm_first_name,
        sfm.last_name AS sfm_last_name,
        sfm.phone AS sfm_phone,
        cm.cm_id,
        cm.first_name AS cm_first_name,
        cm.last_name AS cm_last_name,
        cm.date_of_birth,
        cm.ssn,
        cm.medicare_num,
        cm.phone AS cm_phone,
        cm.address,
        pc.city,
        pc.province,
        cm.postal_code,
        sfm.relationship AS relationship_with_sfm
    FROM sec_fam_members sfm
    JOIN family_club_relations fcr ON sfm.sfm_id = fcr.sfm_id AND sfm.fm_id = fcr.fm_id
    JOIN club_members cm ON fcr.cm_id = cm.cm_id
    JOIN postal_codes pc ON cm.postal_code = pc.postal_code
";

$stmt = $pdo->query($query);
$records = $stmt->fetchAll();
?>

<h2>Secondary Family Members and Linked Club Members</h2>

<?php if (count($records) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Family ID</th>
                <th>Sec. Fam First Name</th>
                <th>Sec. Fam Last Name</th>
                <th>Sec. Fam Phone</th>
                <th>Club Member ID</th>
                <th>Member First Name</th>
                <th>Member Last Name</th>
                <th>Date of Birth</th>
                <th>SSN</th>
                <th>Medicare #</th>
                <th>Member Phone</th>
                <th>Address</th>
                <th>City</th>
                <th>Province</th>
                <th>Postal Code</th>
                <th>Relationship</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['fm_id'] ?></td>
                    <td><?= htmlspecialchars($r['sfm_first_name']) ?></td>
                    <td><?= htmlspecialchars($r['sfm_last_name']) ?></td>
                    <td><?= $r['sfm_phone'] ?></td>
                    <td><?= $r['cm_id'] ?></td>
                    <td><?= htmlspecialchars($r['cm_first_name']) ?></td>
                    <td><?= htmlspecialchars($r['cm_last_name']) ?></td>
                    <td><?= $r['date_of_birth'] ?></td>
                    <td><?= $r['ssn'] ?></td>
                    <td><?= $r['medicare_num'] ?></td>
                    <td><?= $r['cm_phone'] ?></td>
                    <td><?= htmlspecialchars($r['address']) ?></td>
                    <td><?= htmlspecialchars($r['city']) ?></td>
                    <td><?= htmlspecialchars($r['province']) ?></td>
                    <td><?= $r['postal_code'] ?></td>
                    <td><?= $r['relationship_with_sfm'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No secondary family members linked to club members found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
