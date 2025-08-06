<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $stmt = $pdo->query("SELECT COALESCE(MAX(fm_id), 0) + 1 AS next_id FROM family_members");
    $next_id = $stmt->fetchColumn();

    $check = $pdo->prepare("SELECT COUNT(*) FROM postal_codes WHERE postal_code = ?");
    $check->execute([$_POST['postal_code']]);
    if (!$check->fetchColumn()) {
      $insert = $pdo->prepare("INSERT INTO postal_codes (postal_code, city, province) VALUES (?, ?, ?)");
      $insert->execute([$_POST['postal_code'], $_POST['city'], $_POST['province']]);
    }

    $stmt = $pdo->prepare("INSERT INTO family_members (fm_id, first_name, last_name, date_of_birth, ssn, medicare_num, phone, address, postal_code, email, location_id, relationship)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $next_id, $_POST['first_name'], $_POST['last_name'], $_POST['date_of_birth'], $_POST['ssn'], $_POST['medicare_num'],
      $_POST['phone'], $_POST['address'], $_POST['postal_code'], $_POST['email'], $_POST['location_id'], $_POST['relationship']
    ]);

    header("Location: family.php");
    exit;
  } catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
  }
}
?>
<?php include 'header.php'; ?>
<h2>Add New Family Member</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <label>First Name: <input type="text" name="first_name" required></label><br>
  <label>Last Name: <input type="text" name="last_name" required></label><br>
  <label>Date of Birth: <input type="date" name="date_of_birth" required></label><br>
  <label>SSN: <input type="text" name="ssn" required></label><br>
  <label>Medicare Number: <input type="text" name="medicare_num"></label><br>
  <label>Phone: <input type="text" name="phone"></label><br>
  <label>Address: <input type="text" name="address" required></label><br>
  <label>Postal Code: <input type="text" name="postal_code" required></label><br>
  <label>City: <input type="text" name="city" required></label><br>
  <label>Province: <input type="text" name="province" required></label><br>
  <label>Email: <input type="email" name="email"></label><br>
  <label>Location:
    <select name="location_id" required>
      <?php
      $locations = $pdo->query("SELECT location_id, name FROM locations");
      while ($loc = $locations->fetch()) {
        echo "<option value='{$loc['location_id']}'>{$loc['location_id']} - " . htmlspecialchars($loc['name']) . "</option>";
      }
      ?>
    </select>
  </label><br>
  <label>Relationship:
    <select name="relationship" required>
      <option>father</option><option>mother</option><option>grandfather</option><option>grandmother</option>
      <option>tutor</option><option>partner</option><option>friend</option><option>other</option>
    </select>
  </label><br>
  <input type="submit" value="Add Family Member">
</form>
<?php include 'footer.php'; ?>
