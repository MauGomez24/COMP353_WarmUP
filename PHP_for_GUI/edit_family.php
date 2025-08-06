<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing ID");
$fm_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $check = $pdo->prepare("SELECT COUNT(*) FROM postal_codes WHERE postal_code = ?");
    $check->execute([$_POST['postal_code']]);
    if (!$check->fetchColumn()) {
      $insert = $pdo->prepare("INSERT INTO postal_codes (postal_code, city, province) VALUES (?, ?, ?)");
      $insert->execute([$_POST['postal_code'], $_POST['city'], $_POST['province']]);
    }

    $stmt = $pdo->prepare("UPDATE family_members SET first_name=?, last_name=?, date_of_birth=?, ssn=?, medicare_num=?, phone=?, address=?, postal_code=?, email=?, location_id=?, relationship=? WHERE fm_id=?");
    $stmt->execute([
      $_POST['first_name'], $_POST['last_name'], $_POST['date_of_birth'], $_POST['ssn'], $_POST['medicare_num'], $_POST['phone'],
      $_POST['address'], $_POST['postal_code'], $_POST['email'], $_POST['location_id'], $_POST['relationship'], $fm_id
    ]);

    if ($stmt->rowCount() > 0) {
      header("Location: family.php");
      exit;
    } else {
      $message = "⚠️ No changes were made.";
    }
  } catch (PDOException $e) {
    $message = "❌ Error: " . $e->getMessage();
  }
}

$stmt = $pdo->prepare("SELECT f.*, pc.city, pc.province FROM family_members f JOIN postal_codes pc ON f.postal_code = pc.postal_code WHERE fm_id = ?");
$stmt->execute([$fm_id]);
$person = $stmt->fetch();

include 'header.php';
?>
<h2>Edit Family Member</h2>
<?php if (isset($message)) echo "<p style='color:black;'>$message</p>"; ?>
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
  <label>Email: <input type="email" name="email" value="<?= $person['email'] ?>"></label><br>
  <label>Location:
    <select name="location_id" required>
      <?php
      $locations = $pdo->query("SELECT location_id, name FROM locations");
      while ($loc = $locations->fetch()) {
        $selected = ($loc['location_id'] == $person['location_id']) ? 'selected' : '';
        echo "<option value='{$loc['location_id']}' $selected>{$loc['location_id']} - " . htmlspecialchars($loc['name']) . "</option>";
      }
      ?>
    </select>
  </label><br>
  <label>Relationship:
    <select name="relationship">
      <?php
      $options = ['father','mother','grandfather','grandmother','tutor','partner','friend','other'];
      foreach ($options as $opt) {
        $sel = $opt == $person['relationship'] ? 'selected' : '';
        echo "<option value='$opt' $sel>$opt</option>";
      }
      ?>
    </select>
  </label><br>
  <input type="submit" value="Update Family Member">
</form>
<?php include 'footer.php'; ?>
