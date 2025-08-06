<?php
include 'db.php';

if (!isset($_GET['id'])) die("Missing SFM ID");
$sfm_id = $_GET['id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $stmt = $pdo->prepare("UPDATE sec_fam_members SET first_name=?, last_name=?, phone=?, relationship=? WHERE sfm_id=?");
    $stmt->execute([
      $_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['relationship'], $sfm_id
    ]);

    if ($stmt->rowCount() > 0) {
      header("Location: sec_family.php");
      exit;
    } else {
      $message = "No changes made.";
    }
  } catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
  }
}

$stmt = $pdo->prepare("SELECT * FROM sec_fam_members WHERE sfm_id = ?");
$stmt->execute([$sfm_id]);
$person = $stmt->fetch();

include 'header.php';
?>
<h2>Edit Secondary Family Member</h2>
<?php if ($message) echo "<p style='color:black;'>$message</p>"; ?>
<form method="post">
  <label>First Name: <input type="text" name="first_name" value="<?= htmlspecialchars($person['first_name']) ?>" required></label><br>
  <label>Last Name: <input type="text" name="last_name" value="<?= htmlspecialchars($person['last_name']) ?>" required></label><br>
  <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($person['phone']) ?>"></label><br>
  <label>Relationship:
    <select name="relationship" required>
      <?php
      $options = ['father','mother','grandfather','grandmother','tutor','partner','friend','other'];
      foreach ($options as $opt) {
        $sel = ($opt == $person['relationship']) ? 'selected' : '';
        echo "<option value='$opt' $sel>$opt</option>";
      }
      ?>
    </select>
  </label><br>
  <input type="submit" value="Update Secondary Member">
</form>
<?php include 'footer.php'; ?>
