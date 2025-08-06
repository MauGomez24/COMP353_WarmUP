<?php include 'db.php'; ?>
<?php include 'header.php';

if (!isset($_GET['id'])) {
  die("Missing team ID");
}

$team_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $stmt = $pdo->prepare("UPDATE teams SET name=?, score=?, coach_id=?, location_id=?, session_id=? WHERE team_id=?");
    $stmt->execute([
      $_POST['name'],
      $_POST['score'],
      $_POST['coach_id'],
      $_POST['location_id'],
      $_POST['session_id'],
      $team_id
    ]);
    header("Location: teams.php");
    exit;
  } catch (PDOException $e) {
    $error_message = "Error updating team: " . $e->getMessage();
  }
}

$stmt = $pdo->prepare("SELECT * FROM teams WHERE team_id = ?");
$stmt->execute([$team_id]);
$team = $stmt->fetch();

$coaches = $pdo->query("SELECT employee_id, first_name, last_name FROM personnel")->fetchAll();
$locations = $pdo->query("SELECT location_id, name FROM locations")->fetchAll();
$sessions = $pdo->query("SELECT session_id, date FROM sessions")->fetchAll();
?>

<h2>Edit Team</h2>
<?php if (isset($error_message)) echo "<p style='color:red;'>" . htmlspecialchars($error_message) . "</p>"; ?>

<form method="post">
  <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($team['name']) ?>" required></label><br>
  <label>Score: <input type="number" name="score" value="<?= $team['score'] ?>" required></label><br>

  <label>Coach:
    <select name="coach_id" required>
      <?php foreach ($coaches as $c): ?>
        <option value="<?= $c['employee_id'] ?>" <?= $team['coach_id'] == $c['employee_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['first_name'] . " " . $c['last_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Location:
    <select name="location_id" required>
      <?php foreach ($locations as $l): ?>
        <option value="<?= $l['location_id'] ?>" <?= $team['location_id'] == $l['location_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($l['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Session:
    <select name="session_id" required>
      <?php foreach ($sessions as $s): ?>
        <option value="<?= $s['session_id'] ?>" <?= $team['session_id'] == $s['session_id'] ? 'selected' : '' ?>>
          <?= $s['session_id'] . " - " . $s['date'] ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <input type="submit" value="Update Team">
</form>

<?php include 'footer.php'; ?>
