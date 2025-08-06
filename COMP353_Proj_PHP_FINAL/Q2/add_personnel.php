<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $stmt = $pdo->query("SELECT COALESCE(MAX(employee_id), 0) + 1 AS next_id FROM personnel");
    $next_id = $stmt->fetchColumn();

    $check = $pdo->prepare("SELECT COUNT(*) FROM postal_codes WHERE postal_code = ?");
    $check->execute([$_POST['postal_code']]);
    if (!$check->fetchColumn()) {
      $insert_pc = $pdo->prepare("INSERT INTO postal_codes (postal_code, city, province) VALUES (?, ?, ?)");
      $insert_pc->execute([$_POST['postal_code'], $_POST['city'], $_POST['province']]);
    }

    $stmt = $pdo->prepare("INSERT INTO personnel (employee_id, first_name, last_name, date_of_birth, ssn, medicare_num, phone, address, postal_code, email, role, mandate, location_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $next_id, $_POST['first_name'], $_POST['last_name'], $_POST['date_of_birth'], $_POST['ssn'], $_POST['medicare_num'], $_POST['phone'], $_POST['address'],
      $_POST['postal_code'], $_POST['email'], $_POST['role'], $_POST['mandate'], $_POST['location_id']
    ]);
    header("Location: personnel.php");
    exit;
  } catch (PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
  }
}
?>
<?php include 'header.php'; ?>

<h2>Add New Personnel</h2>
<?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

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
  <label>Email: <input type="email" name="email" required></label><br>
  <label>Role:
    <select name="role" required>
      <option>administrator</option>
      <option>captain</option>
      <option>coach</option>
      <option>assistant coach</option>
      <option>general manager</option>
      <option>deputy manager</option>
      <option>treasurer</option>
      <option>secretary</option>
      <option>other</option>
    </select>
  </label><br>
  <label>Mandate:
    <select name="mandate" required>
      <option>volunteer</option>
      <option>salaried</option>
    </select>
  </label><br>
  <label>Location ID: <input type="number" name="location_id" required></label><br>
  <input type="submit" value="Add Personnel">
</form>

<?php include 'footer.php'; ?>
