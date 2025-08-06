<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Secondary Family Members</h2>

<p>
  <a href="family.php">Primary Family Members</a> |
  <a href="sec_family.php">Secondary Family Members</a>
</p>

<p><a href="add_sec_family.php">Add Secondary Family Member</a></p>

<table>
  <tr>
    <th>SFM ID</th>
    <th>Primary FM ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Phone</th>
    <th>Relationship</th>
    <th>Actions</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("
        SELECT s.*, f.first_name AS primary_first, f.last_name AS primary_last
        FROM sec_fam_members s
        JOIN family_members f ON s.fm_id = f.fm_id
    ");

    $refStmt = $pdo->prepare("SELECT COUNT(*) FROM family_club_relations WHERE sfm_id = ?");

    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['sfm_id']}</td>";
        echo "<td>{$row['fm_id']} ({$row['primary_first']} {$row['primary_last']})</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>{$row['phone']}</td>";
        echo "<td>{$row['relationship']}</td>";

        $refStmt->execute([$row['sfm_id']]);
        $linked = $refStmt->fetchColumn() > 0;

        echo "<td>";
        echo "<a href='edit_sec_family.php?id={$row['sfm_id']}'>Edit</a>";
        if (!$linked) {
          echo " | <a href='delete_sec_family.php?id={$row['sfm_id']}' onclick='return confirm('Delete this secondary family member?');'>Delete</a>";
        } else {
          echo " | In Use (Assigned to Club Member)";
        }
        echo "</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
