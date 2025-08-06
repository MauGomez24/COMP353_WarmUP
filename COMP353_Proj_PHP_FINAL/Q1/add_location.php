<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>Add New Location</h2>

<form method="post">
  <label>Name: <input type="text" name="name" required></label><br>
  <label>Address: <input type="text" name="address" required></label><br>
  <label>Postal Code: <input type="text" name="postal_code" required></label><br>
  <label>City: <input type="text" name="city" required></label><br>
  <label>Province: <input type="text" name="province" required></label><br>
  <label>Phone: <input type="text" name="phone"></label><br>
  <label>Web Address: <input type="url" name="web_address" placeholder="https://example.com"></label><br>
  <label>Max Capacity: <input type="number" name="max_capacity" required></label><br>
  <label>Type:
    <select name="is_head">
      <option value="1">Head</option>
      <option value="0">Branch</option>
    </select>
  </label><br>
  <input type="submit" value="Add Location">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Generate next available location ID
        $stmt = $pdo->query("SELECT COALESCE(MAX(location_id), 0) + 1 AS next_id FROM locations");
        $next_id = $stmt->fetchColumn();

        $postal_code = trim($_POST['postal_code']);
        $city = trim($_POST['city']);
        $province = trim($_POST['province']);

        //Check if postal code exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM postal_codes WHERE postal_code = ?");
        $check->execute([$postal_code]);
        $exists = $check->fetchColumn();

        //Insert new postal code
        if (!$exists) {
            $insertPostal = $pdo->prepare("
                INSERT INTO postal_codes (postal_code, city, province)
                VALUES (?, ?, ?)
            ");
            $insertPostal->execute([$postal_code, $city, $province]);
        }

        // Insert new location
        $stmt = $pdo->prepare("
            INSERT INTO locations (location_id, name, address, postal_code, phone, web_address, max_capacity, is_head)
            VALUES (:id, :name, :address, :postal_code, :phone, :web_address, :max_capacity, :is_head)
        ");
        $stmt->execute([
            ':id' => $next_id,
            ':name' => $_POST['name'],
            ':address' => $_POST['address'],
            ':postal_code' => $postal_code,
            ':phone' => $_POST['phone'],
            ':web_address' => $_POST['web_address'],
            ':max_capacity' => $_POST['max_capacity'],
            ':is_head' => $_POST['is_head']
        ]);

        echo "<p>Location added successfully with ID: $next_id</p>";

    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<?php include 'footer.php'; ?>
