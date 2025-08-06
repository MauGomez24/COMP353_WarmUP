<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  try {
    $stmt = $pdo->prepare("INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $_POST['team_id'],
      $_POST['name'],
      $_POST['score'],
      $_POST['coach_id'],
      $_POST['location_id'],
      $_POST['session_id']
    ]);
    header("Location: teams.php");
    exit;
  } catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
      $error_message = "Team ID already exists. Please use a different ID.";
    } else {
      $error_message = $e->getMessage();
    }
  }
}

$coaches = $pdo->query("SELECT employee_id, first_name, last_name FROM personnel")->fetchAll();
$locations = $pdo->query("SELECT location_id, name FROM locations")->fetchAll();
$sessions = $pdo->query("SELECT session_id, date FROM sessions")->fetchAll();

include 'header.php';
?>

<h2>Add New Team</h2>
<?php if (isset($error_message)) echo "<p>Error: " . htmlspecialchars($error_message) . "</p>"; ?>

<form method="post">
  <label>Team ID: <input type="number" name="team_id" required></label><br>
  <label>Name: <input type="text" name="name" required></label><br>
  <label>Score: <input type="number" name="score" required></label><br>

  <label>Coach:
    <select name="coach_id" required>
      <?php foreach ($coaches as $c): ?>
        <option value="<?= $c['employee_id'] ?>"><?= htmlspecialchars($c['first_name'] . " " . $c['last_name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Location:
    <select name="location_id" required>
      <?php foreach ($locations as $l): ?>
        <option value="<?= $l['location_id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Session:
    <select name="session_id" required>
      <?php foreach ($sessions as $s): ?>
        <option value="<?= $s['session_id'] ?>"><?= $s['session_id'] . " - " . $s['date'] ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <input type="submit" value="Add Team">
</form>

<?php include 'footer.php'; ?>
