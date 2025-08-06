<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing member ID");
$cm_id = $_GET['id'];

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

      $checkStmt = $pdo->prepare("SELECT * FROM club_members WHERE cm_id = ?");
      $checkStmt->execute([$cm_id]);
      $current = $checkStmt->fetch();

      $changes = [
        $_POST['first_name'] != $current['first_name'],
        $_POST['last_name'] != $current['last_name'],
        $age != $current['age'],
        $is_minor != $current['is_minor'],
        $_POST['date_of_birth'] != $current['date_of_birth'],
        $_POST['height'] != $current['height'],
        $_POST['weight'] != $current['weight'],
        $_POST['ssn'] != $current['ssn'],
        $_POST['medicare_num'] != $current['medicare_num'],
        $_POST['phone'] != $current['phone'],
        $_POST['email'] != $current['email'],
        $_POST['address'] != $current['address'],
        $_POST['postal_code'] != $current['postal_code'],
        $_POST['gender'] != $current['gender'],
        $_POST['location_id'] != $current['location_id'],
        $fm_id != $current['fm_id']
      ];

      if (!in_array(true, $changes)) {
        $message = "No changes were made.";
      } else {
        $stmt = $pdo->prepare("UPDATE club_members SET 
          first_name=?, last_name=?, age=?, is_minor=?, date_of_birth=?, height=?, weight=?, 
          ssn=?, medicare_num=?, phone=?, email=?, address=?, postal_code=?, gender=?, location_id=?, fm_id=?
          WHERE cm_id=?");

        $stmt->execute([
          $_POST['first_name'], $_POST['last_name'], $age, $is_minor, $_POST['date_of_birth'],
          $_POST['height'], $_POST['weight'], $_POST['ssn'], $_POST['medicare_num'], $_POST['phone'],
          $_POST['email'], $_POST['address'], $_POST['postal_code'], $_POST['gender'], $_POST['location_id'], $fm_id, $cm_id
        ]);

        header("Location: members.php");
        exit;
      }
    }
  } catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
  }
}

$stmt = $pdo->prepare("SELECT * FROM club_members WHERE cm_id = ?");
$stmt->execute([$cm_id]);
$member = $stmt->fetch();

include 'header.php';
?>
<h2>Edit Club Member</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<?php if (isset($message)) echo "<p style='color:black;'>$message</p>"; ?>

<form method="post" onsubmit="return validateForm();">
  <label>First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($member['first_name']) ?>" required></label><br>
  <label>Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($member['last_name']) ?>" required></label><br>
  <label>Age: <input type="number" name="age" id="ageInput" value="<?= $member['age'] ?>" required></label><br>
  <label>Date of Birth: <input type="date" name="date_of_birth" value="<?= $member['date_of_birth'] ?>" required></label><br>
  <label>Height: <input type="number" name="height" value="<?= $member['height'] ?>" required></label><br>
  <label>Weight: <input type="number" name="weight" value="<?= $member['weight'] ?>" required></label><br>
  <label>SSN: <input type="text" name="ssn" value="<?= $member['ssn'] ?>" required></label><br>
  <label>Medicare Number: <input type="text" name="medicare_num" value="<?= $member['medicare_num'] ?>"></label><br>
  <label>Phone: <input type="text" name="phone" value="<?= $member['phone'] ?>"></label><br>
  <label>Email: <input type="email" name="email" value="<?= $member['email'] ?>"></label><br>
  <label>Address: <input type="text" name="address" value="<?= htmlspecialchars($member['address']) ?>" required></label><br>

  <label>Postal Code:
    <select name="postal_code" required>
      <?php
      $codes = $pdo->query("SELECT postal_code, city, province FROM postal_codes");
      while ($row = $codes->fetch()) {
        $selected = $row['postal_code'] === $member['postal_code'] ? 'selected' : '';
        echo "<option value='{$row['postal_code']}' $selected>{$row['postal_code']} - {$row['city']}, {$row['province']}</option>";
      }
      ?>
    </select>
  </label><br>

  <label>Gender:
    <select name="gender" required>
      <option value="M" <?= $member['gender'] === 'M' ? 'selected' : '' ?>>M</option>
      <option value="F" <?= $member['gender'] === 'F' ? 'selected' : '' ?>>F</option>
    </select>
  </label><br>

  <label>Location:
    <select name="location_id" required>
      <?php
      $locs = $pdo->query("SELECT location_id, name FROM locations");
      while ($loc = $locs->fetch()) {
        $selected = $loc['location_id'] == $member['location_id'] ? 'selected' : '';
        echo "<option value='{$loc['location_id']}' $selected>{$loc['location_id']} - " . htmlspecialchars($loc['name']) . "</option>";
      }
      ?>
    </select>
  </label><br>

  <label>Family Member (required if minor):
    <select name="fm_id" id="fm_id">
      <option value="">-- Select --</option>
      <?php
      $fams = $pdo->query("SELECT fm_id, first_name, last_name FROM family_members");
      while ($row = $fams->fetch()) {
        $selected = $member['fm_id'] == $row['fm_id'] ? 'selected' : '';
        echo "<option value='{$row['fm_id']}' $selected>{$row['fm_id']} - " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
      }
      ?>
    </select>
  </label><br>
  <input type="hidden" id="lastFmId" name="last_fm_id" value="<?= $member['fm_id'] ?>">
  <input type="submit" value="Update Member">
</form>

<script>
  const ageInput = document.getElementById('ageInput');
  const fmSelect = document.getElementById('fm_id');
  const lastFm = document.getElementById('lastFmId');

  function validateForm() {
    const age = parseInt(ageInput.value);
    const fmSelected = fmSelect.value;

    if (age < 18 && fmSelected === "") {
      alert("Minors must have a family member.");
      return false;
    }
    if (age >= 18 && fmSelected !== "") {
      alert("Adults must not be associated with a family member.");
      return false;
    }
    return true;
  }

  ageInput.addEventListener('input', function () {
    const age = parseInt(this.value);
    if (age < 18) {
      fmSelect.disabled = false;
      fmSelect.value = lastFm.value;
    } else {
      lastFm.value = fmSelect.value;
      fmSelect.value = "";
      fmSelect.disabled = true;
    }
  });

  window.addEventListener('load', () => {
    const age = parseInt(ageInput.value);
    if (age >= 18) {
      fmSelect.disabled = true;
    }
  });
</script>

<?php include 'footer.php'; ?>
