<?php
include 'db.php';
include 'header.php';

if (!isset($_GET['id'])) {
    die("Error: No personnel ID provided.");
}

$employee_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT h.start_date, h.end_date, l.name AS location_name
        FROM personnel_loc_hist h
        JOIN locations l ON h.location_id = l.location_id
        WHERE h.employee_id = ?
        ORDER BY h.start_date DESC
    ");
    $stmt->execute([$employee_id]);
    $history = $stmt->fetchAll();

    $empStmt = $pdo->prepare("SELECT first_name, last_name FROM personnel WHERE employee_id = ?");
    $empStmt->execute([$employee_id]);
    $person = $empStmt->fetch();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<h2>Location History for <?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?></h2>

<p><a href="personnel.php">← Back to Personnel</a></p>

<?php if (count($history) === 0): ?>
    <p>No history found for this personnel.</p>
<?php else: ?>
<table>
  <tr>
    <th>Location</th>
    <th>Start Date</th>
    <th>End Date</th>
  </tr>
  <?php foreach ($history as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['location_name']) ?></td>
      <td><?= $row['start_date'] ?></td>
      <td><?= $row['end_date'] ?? 'Present' ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php include 'footer.php'; ?>
