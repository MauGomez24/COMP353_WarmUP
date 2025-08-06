<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Payments</h2>

<table>
  <tr>
    <th>CM ID</th>
    <th>Year</th>
    <th>Date</th>
    <th>Amount</th>
    <th>Method</th>
  </tr>

<?php
try {
    $stmt = $pdo->query("SELECT cm_id, memb_year, payment_date, amount, method FROM payments");

    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$row['cm_id']}</td>";
        echo "<td>{$row['memb_year']}</td>";
        echo "<td>{$row['payment_date']}</td>";
        echo "<td>{$row['amount']}</td>";
        echo "<td>{$row['method']}</td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
