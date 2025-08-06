<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Sessions</h2>

<table>
  <tr>
    <th>Session ID</th>
    <th>Type</th>
    <th>Date</th>
    <th>Time</th>
    <th>Location</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("
        SELECT 
            s.session_id,
            s.type,
            s.date,
            s.time,
            l.name AS location_name
        FROM sessions s
        LEFT JOIN locations l ON s.location_id = l.location_id
    ");

    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['session_id']}</td>";
        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
        echo "<td>{$row['date']}</td>";
        echo "<td>{$row['time']}</td>";
        echo "<td>" . htmlspecialchars($row['location_name'] ?? 'Unknown') . "</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
