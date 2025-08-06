<?php
include 'header.php';
include 'db.php';

// Get all triggers for database
$stmt = $pdo->prepare("
    SELECT 
        TRIGGER_NAME,
        EVENT_MANIPULATION AS event_type,
        ACTION_TIMING AS timing,
        EVENT_OBJECT_TABLE AS table_name,
        ACTION_STATEMENT AS definition
    FROM information_schema.triggers
    WHERE TRIGGER_SCHEMA = 'ftc353_1'
    ORDER BY TRIGGER_NAME
");
$stmt->execute();
$triggers = $stmt->fetchAll();
?>

<h2>Triggers Defined in the Database</h2>

<?php if (count($triggers) > 0): ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Trigger Name</th>
                <th>Timing</th>
                <th>Event</th>
                <th>Table</th>
                <th>Trigger Definition</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($triggers as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['TRIGGER_NAME']) ?></td>
                    <td><?= $t['timing'] ?></td>
                    <td><?= $t['event_type'] ?></td>
                    <td><?= $t['table_name'] ?></td>
                    <td><pre><?= htmlspecialchars($t['definition']) ?></pre></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No triggers found in the current database.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
