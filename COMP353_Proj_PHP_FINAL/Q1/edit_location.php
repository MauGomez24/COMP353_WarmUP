<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("Error: No location ID provided.");
}

$location_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $postal_code = trim($_POST['postal_code']);
        $city = trim($_POST['city']);
        $province = trim($_POST['province']);

        // Load current values for full comparison (locations + postal_codes)
        $checkStmt = $pdo->prepare("
            SELECT l.*, pc.city AS pc_city, pc.province AS pc_province
            FROM locations l
            JOIN postal_codes pc ON l.postal_code = pc.postal_code
            WHERE l.location_id = ?
        ");
        $checkStmt->execute([$location_id]);
        $current = $checkStmt->fetch();

        // Detect changes
        $changes = [
            $_POST['name'] != $current['name'],
            $_POST['address'] != $current['address'],
            $postal_code != $current['postal_code'],
            $_POST['city'] != $current['pc_city'],
            $_POST['province'] != $current['pc_province'],
            $_POST['phone'] != $current['phone'],
            $_POST['web_address'] != $current['web_address'],
            $_POST['max_capacity'] != $current['max_capacity'],
            $_POST['is_head'] != $current['is_head']
        ];

        if (!in_array(true, $changes)) {
            $message = "No changes were made.";
        } else {
            // Ensure postal code is in postal_codes table
            $check = $pdo->prepare("SELECT COUNT(*) FROM postal_codes WHERE postal_code = ?");
            $check->execute([$postal_code]);
            $exists = $check->fetchColumn();

            if (!$exists) {
                $insertPostal = $pdo->prepare("
                    INSERT INTO postal_codes (postal_code, city, province)
                    VALUES (?, ?, ?)
                ");
                $insertPostal->execute([$postal_code, $city, $province]);
            } else {
                $updatePostal = $pdo->prepare("
                    UPDATE postal_codes SET city = ?, province = ? WHERE postal_code = ?
                ");
                $updatePostal->execute([$city, $province, $postal_code]);
            }

            // Update location
            $stmt = $pdo->prepare("
                UPDATE locations SET
                    name = :name,
                    address = :address,
                    postal_code = :postal_code,
                    phone = :phone,
                    web_address = :web_address,
                    max_capacity = :max_capacity,
                    is_head = :is_head
                WHERE location_id = :id
            ");
            $stmt->execute([
                ':name' => $_POST['name'],
                ':address' => $_POST['address'],
                ':postal_code' => $postal_code,
                ':phone' => $_POST['phone'],
                ':web_address' => $_POST['web_address'],
                ':max_capacity' => $_POST['max_capacity'],
                ':is_head' => $_POST['is_head'],
                ':id' => $location_id
            ]);

            header("Location: locations.php");
            exit;
        }

    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Load current values for form
$stmt = $pdo->prepare("
    SELECT l.*, pc.city, pc.province
    FROM locations l
    JOIN postal_codes pc ON l.postal_code = pc.postal_code
    WHERE l.location_id = ?
");
$stmt->execute([$location_id]);
$location = $stmt->fetch();

if (!$location) {
    die("Location not found.");
}
?>

<?php include 'header.php'; ?>

<h2>Edit Location</h2>
<?php if (isset($message)) echo "<p style='color:black;'>$message</p>"; ?>

<form method="post">
  <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($location['name']) ?>" required></label><br>
  <label>Address: <input type="text" name="address" value="<?= htmlspecialchars($location['address']) ?>" required></label><br>
  <label>Postal Code: <input type="text" name="postal_code" value="<?= htmlspecialchars($location['postal_code']) ?>" required></label><br>
  <label>City: <input type="text" name="city" value="<?= htmlspecialchars($location['city']) ?>" required></label><br>
  <label>Province: <input type="text" name="province" value="<?= htmlspecialchars($location['province']) ?>" required></label><br>
  <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($location['phone']) ?>"></label><br>
  <label>Web Address: <input type="url" name="web_address" value="<?= htmlspecialchars($location['web_address']) ?>"></label><br>
  <label>Max Capacity: <input type="number" name="max_capacity" value="<?= $location['max_capacity'] ?>" required></label><br>
  <label>Type:
    <select name="is_head">
      <option value="1" <?= $location['is_head'] ? 'selected' : '' ?>>Head</option>
      <option value="0" <?= !$location['is_head'] ? 'selected' : '' ?>>Branch</option>
    </select>
  </label><br>
  <input type="submit" value="Update Location">
</form>

<?php include 'footer.php'; ?>
	
