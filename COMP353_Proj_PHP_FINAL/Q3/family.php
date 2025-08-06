<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Primary Family Members</h2>

<p>
  <a href="family.php">Primary Family Members</a> |
  <a href="sec_family.php">Secondary Family Members</a>
</p>

<p><a href="add_family.php">Add New Family Member</a></p>

<table>
  <tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>DOB</th>
    <th>SSN</th>
    <th>Medicare #</th>
    <th>Phone</th>
    <th>Address</th>
    <th>City</th>
    <th>Province</th>
    <th>Postal Code</th>
    <th>Email</th>
    <th>Location</th>
    <th>Relationship</th>
    <th>Actions</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("
        SELECT f.*, pc.city, pc.province, l.name AS location_name
        FROM family_members f
        JOIN postal_codes pc ON f.postal_code = pc.postal_code
        JOIN locations l ON f.location_id = l.location_id
    ");

    // Fetch all fm_id references
    $used_in_club = $pdo->query("SELECT DISTINCT fm_id FROM club_members WHERE fm_id IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
    $used_in_secondary = $pdo->query("SELECT DISTINCT fm_id FROM sec_fam_members")->fetchAll(PDO::FETCH_COLUMN);
    $used_in_relations = $pdo->query("SELECT DISTINCT fm_id FROM family_club_relations")->fetchAll(PDO::FETCH_COLUMN);

    while ($row = $stmt->fetch()) {
        $fm_id = $row['fm_id'];
        $in_club = in_array($fm_id, $used_in_club);
        $in_secondary = in_array($fm_id, $used_in_secondary);
        $in_relation = in_array($fm_id, $used_in_relations);

        echo "<tr>";
        echo "<td>{$fm_id}</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>{$row['date_of_birth']}</td>";
        echo "<td>{$row['ssn']}</td>";
        echo "<td>{$row['medicare_num']}</td>";
        echo "<td>{$row['phone']}</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['city']) . "</td>";
        echo "<td>" . htmlspecialchars($row['province']) . "</td>";
        echo "<td>{$row['postal_code']}</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location_name']) . "</td>";
        echo "<td>{$row['relationship']}</td>";
        echo "<td><a href='edit_family.php?id={$fm_id}'>Edit</a>";

        if ($in_club && $in_secondary && $in_relation) {
            echo " | In Use (Club, Secondary Family Member, 3-Way Mapping with Club and Sec Family)</span>";
        } elseif ($in_club && $in_secondary) {
            echo " | In Use (Club, Secondary Family Member)</span>";
        } elseif ($in_club && $in_relation) {
            echo " | In Use (Club, 3-Way Mapping with Club and Sec Family)</span>";
        } elseif ($in_secondary && $in_relation) {
            echo " | In Use (Secondary Family Member, 3-Way Mapping with Club and Sec Family)</span>";
        } elseif ($in_club) {
            echo " | In Use (Club)</span>";
        } elseif ($in_secondary) {
            echo " | In Use (Secondary Family Member)</span>";
        } elseif ($in_relation) {
            echo " | In Use (3-Way Mapping with Club and Sec Family)</span>";
        } else {
            echo " | <a href='delete_family.php?id={$fm_id}' onclick='return confirm('Are you sure you want to delete this family member?');'>Delete</a>";
        }

        echo "</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='15'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
