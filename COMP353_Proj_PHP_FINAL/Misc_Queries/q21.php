<?php
include 'header.php';
include 'db.php';

/**
 * Executes and describes a SQL insert statement
 */
function tryInsert($pdo, $label, $description, $sql, $params = []) {
    echo "<h3>$label</h3>";
    echo "<p><strong>What is being attempted:</strong> $description</p>";
    echo "<p><strong>SQL being executed:</strong></p>";
    echo "<pre><code>" . htmlspecialchars($sql) . "</code></pre>";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo "<p><strong> Success:</strong> Insert executed without errors.</p>";
    } catch (PDOException $e) {
        echo "<p><strong> Failed:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "<hr>";
}

echo "<h2>Task 21: Trigger-Based System Integrity Demonstration</h2>";

/** CLEANUP **/
tryInsert($pdo, "Cleanup: team_players", "Removing test entries in team_players", "DELETE FROM team_players WHERE team_id IN (2001, 2002)");
tryInsert($pdo, "Cleanup: teams", "Removing test teams", "DELETE FROM teams WHERE team_id IN (2001, 2002)");
tryInsert($pdo, "Cleanup: sessions", "Removing test sessions", "DELETE FROM sessions WHERE session_id IN (1001, 1002)");

/** TEST 1: Invalid Coach Assignment **/
tryInsert(
    $pdo,
    "Test 1: Invalid Coach Assignment",
    "Trying to insert a team with coach_id = 1 (not a valid coach) to trigger 'validate_coach_id'.",
    "INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
     VALUES (2001, 'Team A', NULL, 1, 1, 1)"
);

/** TEST 2: Scheduling Conflict Trigger **/
tryInsert(
    $pdo,
    "Test 2.1: Create Session 1",
    "Creating session 1001 on 2025-08-05 at 10:00:00.",
    "INSERT INTO sessions (session_id, type, date, time, location_id)
     VALUES (1001, 'train', '2025-08-05', '10:00:00', 1)"
);

tryInsert(
    $pdo,
    "Test 2.2: Create Session 2",
    "Creating session 1002 on 2025-08-05 at 11:30:00 (only 1.5 hours apart).",
    "INSERT INTO sessions (session_id, type, date, time, location_id)
     VALUES (1002, 'train', '2025-08-05', '11:30:00', 1)"
);

tryInsert(
    $pdo,
    "Test 2.3: Create Team A",
    "Assigning valid coach_id = 62 to team A at session 1001.",
    "INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
     VALUES (2001, 'Team A', NULL, 62, 1, 1001)"
);

tryInsert(
    $pdo,
    "Test 2.4: Create Team B",
    "Assigning valid coach_id = 62 to team B at session 1002.",
    "INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
     VALUES (2002, 'Team B', NULL, 62, 1, 1002)"
);

tryInsert(
    $pdo,
    "Test 2.5: Assign player to Team A",
    "Assigning cm_id = 10 to team A (no trigger violation expected).",
    "INSERT INTO team_players (cm_id, team_id, role)
     VALUES (10, 2001, 'setter')"
);

tryInsert(
    $pdo,
    "Test 2.6: Assign same player to Team B (conflict expected)",
    "Attempting to assign cm_id = 10 to team B which is < 3 hours apart from session A — should trigger 'prevent_conflicting_assignments'.",
    "INSERT INTO team_players (cm_id, team_id, role)
     VALUES (10, 2002, 'libero')"
);

include 'footer.php';
?>
