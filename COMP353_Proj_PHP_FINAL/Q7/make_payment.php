<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cm_id = $_POST['cm_id'];
    $memb_year = $_POST['memb_year'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $method = $_POST['method'];

    $stmt = $pdo->prepare("
        INSERT INTO payments (cm_id, memb_year, payment_date, amount, method)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$cm_id, $memb_year, $payment_date, $amount, $method]);

    $message = "Payment successfully recorded.";
}

include 'header.php';

$members = $pdo->query("SELECT cm_id, first_name, last_name FROM club_members ORDER BY last_name")->fetchAll();
$methods = ['credit', 'debit', 'cash'];
$today = date('Y-m-d');
$currentYear = date('Y');
?>

<h2>Make Payment for a Club Member</h2>

<form method="POST">
    <label for="cm_id">Select Club Member:</label><br>
    <select name="cm_id" required>
        <?php foreach ($members as $member): ?>
            <option value="<?= $member['cm_id'] ?>">
                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?> (ID: <?= $member['cm_id'] ?>)
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="memb_year">Membership Year:</label><br>
    <input type="number" name="memb_year" value="<?= $currentYear ?>" required><br><br>

    <label for="payment_date">Payment Date:</label><br>
    <input type="date" name="payment_date" value="<?= $today ?>" required><br><br>

    <label for="amount">Amount ($):</label><br>
    <input type="number" name="amount" step="0.01" max="999.99" required><br><br>

    <label for="method">Payment Method:</label><br>
    <select name="method" required>
        <?php foreach ($methods as $m): ?>
            <option value="<?= $m ?>"><?= ucfirst($m) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="submit" value="Submit Payment">
</form>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<a href="payments.php">← Back to Payment Records</a>

<?php include 'footer.php'; ?>
