<?php include 'db.php'; ?>
<?php include 'header.php'; ?>

<h2>All Teams</h2>

<p><a href="add_team.php">Add New Team</a></p>

<table>
  <tr>
    <th>Team ID</th>
    <th>Name</th>
    <th>Score</th>
    <th>Coach</th>
    <th>Location</th>
    <th>Session</th>
    <th>Actions</th>
  </tr>

<?php
try {
    // Get list of in-use team_ids
    $used_team_ids = $pdo->query("SELECT DISTINCT team_id FROM team_players")->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->query("
        SELECT t.*, p.first_name AS coach_fname, p.last_name AS coach_lname, 
               l.name AS location_name, s.date AS session_date, s.type AS session_type
        FROM teams t
        LEFT JOIN personnel p ON t.coach_id = p.employee_id
        LEFT JOIN locations l ON t.location_id = l.location_id
        LEFT JOIN sessions s ON t.session_id = s.session_id
    ");

    while ($row = $stmt->fetch()) {
        $team_id = $row['team_id'];
        $in_use = in_array($team_id, $used_team_ids);

        echo "<tr>";
        echo "<td>{$team_id}</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>{$row['score']}</td>";
        echo "<td>" . htmlspecialchars("{$row['coach_fname']} {$row['coach_lname']}") . "</td>";
        echo "<td>" . htmlspecialchars($row['location_name']) . "</td>";
        echo "<td>" . htmlspecialchars("{$row['session_type']} on {$row['session_date']}") . "</td>";
        echo "<td>
                <a href='edit_team.php?id={$team_id}'>Edit</a>";

        if ($in_use) {
            echo " | In Use (Club members using TeamID)</span>";
        } else {
	    echo " | <a href='delete_team.php?id={$team_id}' onclick=\"return confirm('Are you sure you want to delete this team?');\">Delete</a>";
        }

        echo "</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
}
?>

</table>

<?php include 'footer.php'; ?>
