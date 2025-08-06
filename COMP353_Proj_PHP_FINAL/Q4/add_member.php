<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $age = (int)$_POST['age'];
    $is_minor = $age < 18 ? 1 : 0;
    $fm_id = isset($_POST['fm_id']) && is_numeric($_POST['fm_id']) ? (int)$_POST['fm_id'] : null;

    if ($is_minor && is_null($fm_id)) {
      $error = "Minors must have a family member.";
    } elseif (!$is_minor && !is_null($fm_id)) {
      $error = "Adults must not be associated with a family member.";
    } else {
      if (!$is_minor) $fm_id = null;

      $stmt = $pdo->prepare("INSERT INTO club_members 
        (first_name, last_name, age, is_minor, date_of_birth, height, weight, ssn, medicare_num, phone, email, address, postal_code, gender, location_id, fm_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $stmt->execute([
        $_POST['first_name'], $_POST['last_name'], $age, $is_minor, $_POST['date_of_birth'],
        $_POST['height'], $_POST['weight'], $_POST['ssn'], $_POST['medicare_num'], $_POST['phone'], $_POST['email'],
        $_POST['address'], $_POST['postal_code'], $_POST['gender'], $_POST['location_id'], $fm_id
      ]);

      header("Location: members.php");
      exit;
    }
  } catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
  }
}
?>
<?php include 'header.php'; ?>
<h2>Add Club Member</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
  <label>First Name: <input type="text" name="first_name" required></label><br>
  <label>Last Name: <input type="text" name="last_name" required></label><br>
  <label>Age: <input type="number" name="age" id="ageInput" required></label><br>
  <label>Date of Birth: <input type="date" name="date_of_birth" required></label><br>
  <label>Height: <input type="number" name="height" required></label><br>
  <label>Weight: <input type="number" name="weight" required></label><br>
  <label>SSN: <input type="text" name="ssn" required></label><br>
  <label>Medicare Number: <input type="text" name="medicare_num"></label><br>
  <label>Phone: <input type="text" name="phone"></label><br>
  <label>Email: <input type="email" name="email"></label><br>
  <label>Address: <input type="text" name="address" required></label><br>

  <label>Postal Code:
    <select name="postal_code" required>
      <option value="">-- Select Postal Code --</option>
      <?php
      $codes = $pdo->query("SELECT postal_code, city, province FROM postal_codes");
      while ($row = $codes->fetch()) {
        echo "<option value='{$row['postal_code']}'>{$row['postal_code']} - {$row['city']}, {$row['province']}</option>";
      }
      ?>
    </select>
  </label><br>

  <label>Gender:
    <select name="gender" required>
      <option value="M">M</option>
      <option value="F">F</option>
    </select>
  </label><br>

  <label>Location:
    <select name="location_id" required>
      <option value="">-- Select Location --</option>
      <?php
      $locs = $pdo->query("SELECT location_id, name FROM locations");
      while ($loc = $locs->fetch()) {
        echo "<option value='{$loc['location_id']}'>{$loc['location_id']} - " . htmlspecialchars($loc['name']) . "</option>";
      }
      ?>
    </select>
  </label><br>

  <div id="fmSection" style="display:none;">
    <label>Family Member (required if minor):
      <select name="fm_id" id="fm_id">
        <option value="">-- Select --</option>
        <?php
        $fams = $pdo->query("SELECT fm_id, first_name, last_name FROM family_members");
        while ($row = $fams->fetch()) {
          echo "<option value='{$row['fm_id']}'>{$row['fm_id']} - " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
        }
        ?>
      </select>
    </label><br>
  </div>

  <input type="submit" value="Add Member">
</form>

<script>
  const ageInput = document.getElementById('ageInput');
  const fmSection = document.getElementById('fmSection');
  const fmSelect = document.getElementById('fm_id');

  ageInput.addEventListener('input', function () {
    const age = parseInt(this.value);
    fmSection.style.display = (age < 18) ? 'block' : 'none';
  });
</script>

<?php include 'footer.php'; ?>
