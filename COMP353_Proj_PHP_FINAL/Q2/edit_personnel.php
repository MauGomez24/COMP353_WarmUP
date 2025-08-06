<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing personnel ID");
$employee_id = $_GET['id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    // Ensure postal code exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM postal_codes WHERE postal_code = ?");
    $check->execute([$_POST['postal_code']]);
    if (!$check->fetchColumn()) {
      $insert_pc = $pdo->prepare("INSERT INTO postal_codes (postal_code, city, province) VALUES (?, ?, ?)");
      $insert_pc->execute([$_POST['postal_code'], $_POST['city'], $_POST['province']]);
    }

    // Perform the update
    $stmt = $pdo->prepare("UPDATE personnel SET first_name=?, last_name=?, date_of_birth=?, ssn=?, medicare_num=?, phone=?, address=?, postal_code=?, email=?, role=?, mandate=?, location_id=? WHERE employee_id=?");
    $stmt->execute([
      $_POST['first_name'], $_POST['last_name'], $_POST['date_of_birth'], $_POST['ssn'], $_POST['medicare_num'], $_POST['phone'],
      $_POST['address'], $_POST['postal_code'], $_POST['email'], $_POST['role'], $_POST['mandate'], $_POST['location_id'], $employee_id
    ]);

    // ✅ Check row count
    if ($stmt->rowCount() > 0) {
      header("Location: personnel.php");
      exit;
    } else {
      $message = "No changes were made. All values are the same.";
    }

  } catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
  }
}

// Load current record
$stmt = $pdo->prepare("SELECT p.*, pc.city, pc.province FROM personnel p JOIN postal_codes pc ON p.postal_code = pc.postal_code WHERE p.employee_id = ?");
$stmt->execute([$employee_id]);
$person = $stmt->fetch();

include 'header.php';
?>
<h2>Edit Personnel</h2>
<?php if ($message) echo "<p style='color:black;'>$message</p>"; ?>

<form method="post">
  <label>First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($person['first_name']) ?>" required></label><br>
  <label>Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($person['last_name']) ?>" required></label><br>
  <label>Date of Birth: <input type="date" name="date_of_birth" value="<?= $person['date_of_birth'] ?>" required></label><br>
  <label>SSN: <input type="text" name="ssn" value="<?= $person['ssn'] ?>" required></label><br>
  <label>Medicare Number: <input type="text" name="medicare_num" value="<?= $person['medicare_num'] ?>"></label><br>
  <label>Phone: <input type="text" name="phone" value="<?= $person['phone'] ?>"></label><br>
  <label>Address: <input type="text" name="address" value="<?= htmlspecialchars($person['address']) ?>" required></label><br>
  <label>Postal Code: <input type="text" name="postal_code" value="<?= $person['postal_code'] ?>" required></label><br>
  <label>City: <input type="text" name="city" value="<?= htmlspecialchars($person['city']) ?>" required></label><br>
  <label>Province: <input type="text" name="province" value="<?= htmlspecialchars($person['province']) ?>" required></label><br>
  <label>Email: <input type="email" name="email" value="<?= $person['email'] ?>" required></label><br>
  <label>Role: 
    <select name="role">
      <?php
      $roles = ['administrator', 'captain', 'coach', 'assistant coach', 'general manager', 'deputy manager', 'treasurer', 'secretary', 'other'];
      foreach ($roles as $role) {
        $sel = ($role == $person['role']) ? 'selected' : '';
        echo "<option $sel>$role</option>";
      }
      ?>
    </select>
  </label><br>
  <label>Mandate:
    <select name="mandate">
      <option value="volunteer" <?= $person['mandate'] == 'volunteer' ? 'selected' : '' ?>>volunteer</option>
      <option value="salaried" <?= $person['mandate'] == 'salaried' ? 'selected' : '' ?>>salaried</option>
    </select>
  </label><br>
  <label>Location ID: <input type="number" name="location_id" value="<?= $person['location_id'] ?>" required></label><br>
  <input type="submit" value="Update Personnel">
</form>

<?php include 'footer.php'; ?>
