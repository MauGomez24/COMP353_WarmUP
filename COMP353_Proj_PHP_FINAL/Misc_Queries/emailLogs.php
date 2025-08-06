<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>Email Logs</h2>

<table>
  <tr>
    <th>Email ID</th>
    <th>Date</th>
    <th>Sender Location</th>
    <th>Receiver (Club Member)</th>
    <th>Subject</th>
    <th>Body Preview</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("
        SELECT 
            e.email_id,
            e.date,
            l.name AS sender_location,
            CONCAT(cm.first_name, ' ', cm.last_name) AS receiver_name,
            e.subject,
            e.body
        FROM email_log e
        LEFT JOIN locations l ON e.sender_loc_id = l.location_id
        LEFT JOIN club_members cm ON e.receiver_cm_id = cm.cm_id
    ");

    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['email_id']}</td>";
        echo "<td>{$row['date']}</td>";
        echo "<td>" . htmlspecialchars($row['sender_location'] ?? 'Unknown') . "</td>";
        echo "<td>" . htmlspecialchars($row['receiver_name'] ?? 'Unknown') . "</td>";
        echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['body'], 0, 100)) . "...</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
