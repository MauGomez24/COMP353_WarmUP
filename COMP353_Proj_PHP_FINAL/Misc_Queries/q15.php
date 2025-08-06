<?php
include 'header.php';
include 'db.php';

$query = "SELECT * FROM always_setter_posn";
$stmt = $pdo->query($query);
$results = $stmt->fetchAll();
?>

<h2>Query Result: always_setter_posn</h2>

<?php if (count($results) > 0): ?>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <?php foreach (array_keys($results[0]) as $col): ?>
                <th><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <?php foreach ($row as $val): ?>
                    <td><?= htmlspecialchars($val) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
