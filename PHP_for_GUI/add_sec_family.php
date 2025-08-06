<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $stmt = $pdo->query("SELECT COALESCE(MAX(sfm_id), 0) + 1 AS next_id FROM sec_fam_members");
    $next_id = $stmt->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO sec_fam_members (sfm_id, fm_id, first_name, last_name, phone, relationship)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $next_id, $_POST['fm_id'], $_POST['first_name'], $_POST['last_name'],
      $_POST['phone'], $_POST['relationship']
    ]);

    header("Location: sec_family.php");
    exit;
  } catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
  }
}
?>
<?php include 'header.php'; ?>
<h2>Add Secondary Family Member</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <label>Primary Family Member:
    <select name="fm_id" required>
      <?php
      $stmt = $pdo->query("SELECT fm_id, first_name, last_name FROM family_members
                           WHERE fm_id NOT IN (SELECT fm_id FROM sec_fam_members)");
      while ($row = $stmt->fetch()) {
        echo "<option value='{$row['fm_id']}'>{$row['fm_id']} - " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
      }
      ?>
    </select>
  </label><br>
  <label>First Name: <input type="text" name="first_name" required></label><br>
  <label>Last Name: <input type="text" name="last_name" required></label><br>
  <label>Phone: <input type="text" name="phone"></label><br>
  <label>Relationship:
    <select name="relationship" required>
      <option>father</option><option>mother</option><option>grandfather</option><option>grandmother</option>
      <option>tutor</option><option>partner</option><option>friend</option><option>other</option>
    </select>
  </label><br>
  <input type="submit" value="Add Secondary Member">
</form>
<?php include 'footer.php'; ?>
