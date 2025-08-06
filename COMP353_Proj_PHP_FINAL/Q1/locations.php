<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Locations</h2>

<p><a href="add_location.php">Add New Location</a></p>

<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Address</th>
    <th>City</th>
    <th>Province</th>
    <th>Postal Code</th>
    <th>Phone</th>
    <th>Web Address</th>
    <th>Max Capacity</th>
    <th>Type</th>
    <th>Actions</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("
        SELECT
            l.location_id,
            l.name,
            l.address,
            pc.city,
            pc.province,
            l.postal_code,
            l.phone,
            l.web_address,
            l.max_capacity,
            l.is_head
        FROM locations l
        JOIN postal_codes pc ON l.postal_code = pc.postal_code
    ");

    // Collect in-use location_ids
    $location_use_stmt = $pdo->query("
      SELECT DISTINCT location_id FROM (
        SELECT location_id FROM personnel
        UNION
        SELECT location_id FROM family_members
        UNION
        SELECT location_id FROM club_members
        UNION
        SELECT location_id FROM sessions
        UNION
        SELECT location_id FROM teams
      ) AS used_locations
    ");
    $used_location_ids = $location_use_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Prepare statement to check if postal_code is used outside this location
    $check_postal = $pdo->prepare("
      SELECT 1 FROM (
        SELECT postal_code FROM personnel
        UNION
        SELECT postal_code FROM family_members
        UNION
        SELECT postal_code FROM club_members
      ) AS external_postals
      WHERE postal_code = ?
      LIMIT 1
    ");

    while ($row = $stmt->fetch()) {
        $location_id = $row['location_id'];
        $postal_code = $row['postal_code'];
        $location_used = in_array($location_id, $used_location_ids);

        // check if postal code is in use elsewhere
        $check_postal->execute([$postal_code]);
        $postal_used_elsewhere = $check_postal->fetchColumn() ? true : false;

        echo '<tr>';
        echo '<td>' . $location_id . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['address']) . '</td>';
        echo '<td>' . htmlspecialchars($row['city']) . '</td>';
        echo '<td>' . htmlspecialchars($row['province']) . '</td>';
        echo '<td>' . $postal_code . '</td>';
        echo '<td>' . $row['phone'] . '</td>';
        echo '<td><a href="' . htmlspecialchars($row['web_address']) . '" target="_blank">' . htmlspecialchars($row['web_address']) . '</a></td>';
        echo '<td>' . $row['max_capacity'] . '</td>';
        echo '<td>' . ($row['is_head'] ? 'Head' : 'Branch') . '</td>';

        echo "<td><a href='edit_location.php?id=$location_id'>Edit</a>";

        if ($location_used && $postal_used_elsewhere) {
            echo '    In Use (Location ID / Postal Code)';
        } elseif ($location_used) {
            echo '    In Use (Location ID)';
        } elseif ($postal_used_elsewhere) {
            echo '    In Use (Postal Code)';
        } else {
			echo ' | <a href="delete_location.php?id=' . $location_id . '" onclick="return confirm(\'Are you sure you want to delete this location?\');">Delete</a>';
        }

        echo '</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="11">Error: ' . $e->getMessage() . '</td></tr>';
}
?>

</table>

<?php include 'footer.php'; ?>
