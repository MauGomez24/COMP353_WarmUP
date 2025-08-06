<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Club Members</h2>

<p><a href="add_member.php">Add New Member</a></p>

<p>
  <a href="members.php">All</a> |
  <a href="members.php?minor=1">Minors Only</a> |
  <a href="members.php?minor=0">Majors Only</a>
</p>

<table>
  <tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Age</th>
    <th>Minor</th>
    <th>DOB</th>
    <th>Gender</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Address</th>
    <th>Postal Code</th>
    <th>Height</th>
    <th>Weight</th>
    <th>Location</th>
    <th>Actions</th>
  </tr>

<?php
try {
    $filter = "";
    if (isset($_GET['minor']) && ($_GET['minor'] === '0' || $_GET['minor'] === '1')) {
        $filter = "WHERE cm.is_minor = " . intval($_GET['minor']);
    }

    $stmt = $pdo->query("
        SELECT cm.*, pc.city, pc.province, l.name AS location_name
        FROM club_members cm
        JOIN postal_codes pc ON cm.postal_code = pc.postal_code
        JOIN locations l ON cm.location_id = l.location_id
        $filter
    ");

    $used_in_payments = $pdo->query("SELECT DISTINCT cm_id FROM payments")->fetchAll(PDO::FETCH_COLUMN);
    $used_in_team_players = $pdo->query("SELECT DISTINCT cm_id FROM team_players")->fetchAll(PDO::FETCH_COLUMN);
    $used_in_fcr = $pdo->query("SELECT DISTINCT cm_id FROM family_club_relations")->fetchAll(PDO::FETCH_COLUMN);
    $used_in_emails = $pdo->query("SELECT DISTINCT receiver_cm_id FROM email_log")->fetchAll(PDO::FETCH_COLUMN);

    while ($row = $stmt->fetch()) {
        $cm_id = $row['cm_id'];
        $used1 = in_array($cm_id, $used_in_payments);
        $used2 = in_array($cm_id, $used_in_team_players);
        $used3 = in_array($cm_id, $used_in_fcr);
        $used4 = in_array($cm_id, $used_in_emails);

        echo "<tr>";
        echo "<td>{$cm_id}</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>{$row['age']}</td>";
        echo "<td>" . ($row['is_minor'] ? 'Yes' : 'No') . "</td>";
        echo "<td>{$row['date_of_birth']}</td>";
        echo "<td>{$row['gender']}</td>";
        echo "<td>{$row['phone']}</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
        echo "<td>{$row['postal_code']}</td>";
        echo "<td>{$row['height']}</td>";
        echo "<td>{$row['weight']}</td>";
        echo "<td>" . htmlspecialchars($row['location_name']) . "</td>";
        echo "<td><a href='edit_member.php?id={$cm_id}'>Edit</a>";

        if ($used1 || $used2 || $used3 || $used4) {
            echo " | In Use (";
            $labels = [];
            if ($used1) $labels[] = "Payments";
            if ($used2) $labels[] = "Team";
            if ($used3) $labels[] = "Associated to family member";
            if ($used4) $labels[] = "Found in Email Log";
            echo implode(", ", $labels) . ")</span>";
        } else {
            echo " | <a href='delete_member.php?id={$cm_id}' onclick='return confirm(\'Are you sure you want to delete this member?\');'>Delete</a>";
        }

        echo "</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='15'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
