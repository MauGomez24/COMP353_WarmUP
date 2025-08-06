<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Personnel</h2>

<p><a href="add_personnel.php">Add New Personnel</a></p>

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
    <th>Role</th>
    <th>Mandate</th>
    <th>Location</th>
    <th>Actions</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("
        SELECT
            p.employee_id,
            p.first_name,
            p.last_name,
            p.date_of_birth,
            p.ssn,
            p.medicare_num,
            p.phone,
            p.address,
            pc.city,
            pc.province,
            p.postal_code,
            p.email,
            p.role,
            p.mandate,
            l.name AS location_name
        FROM personnel p
        JOIN postal_codes pc ON p.postal_code = pc.postal_code
        JOIN locations l ON p.location_id = l.location_id
    ");

    $coach_ids = $pdo->query("SELECT DISTINCT coach_id FROM teams")->fetchAll(PDO::FETCH_COLUMN);
    $hist_ids = $pdo->query("SELECT DISTINCT employee_id FROM personnel_loc_hist")->fetchAll(PDO::FETCH_COLUMN);

    while ($row = $stmt->fetch()) {
        $id = $row['employee_id'];
        $is_coach = in_array($id, $coach_ids);
        $in_history = in_array($id, $hist_ids);

        echo "<tr>";
        echo "<td>{$id}</td>";
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
        echo "<td>{$row['role']}</td>";
        echo "<td>{$row['mandate']}</td>";
        echo "<td>" . htmlspecialchars($row['location_name']) . "</td>";
        echo "<td>";
        echo "<a href='edit_personnel.php?id={$id}'>Edit</a> | ";
        echo "<a href='personnel_history.php?id={$id}'>View Personell History</a>";

        if ($is_coach && $in_history) {
            echo " | In Use (Coach & History)";
        } elseif ($is_coach) {
            echo " | In Use (Coach)";
        } elseif ($in_history) {
            echo " | In Use (History)";
        } else {
            echo " | <a href='delete_personnel.php?id={$id}' onclick='return confirm('Are you sure you want to delete this personnel?');'>Delete</a>";
        }

        echo "</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='16'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
