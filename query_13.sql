-- query 13
DROP VIEW IF EXISTS view_active_members_without_team_assignments;
CREATE VIEW view_active_members_without_team_assignments AS

-- Delete any existing data for the dummy team_id = 999 to avoid duplicate entry errors
DELETE FROM team_players WHERE team_id = 999;
DELETE FROM teams WHERE team_id = 999;

-- Insert a new team called 'Phantoms' that is not assigned to any session (session_id = NULL)
-- This allows us to simulate members who are in a team but not scheduled for training/game
INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
VALUES (999, 'Phantoms', NULL, 18, 1, NULL);

-- Assign three club members (IDs 49, 50, 51) to this team
-- These members are technically "in a team" but not in any session
INSERT INTO team_players (cm_id, team_id, role) VALUES
(49, 999, 'setter'),
(50, 999, 'libero'),
(51, 999, 'middle blocker');

SELECT 
    cm.cm_id,
    cm.first_name,
    cm.last_name,
    TIMESTAMPDIFF(YEAR, cm.date_of_birth, CURDATE()) AS age,
    cm.phone,
    cm.email,
    l.name AS location_name
FROM club_members cm
JOIN locations l ON cm.location_id = l.location_id
WHERE cm.cm_id IN (
    SELECT tp.cm_id
    FROM team_players tp
    JOIN teams t ON tp.team_id = t.team_id
    WHERE t.session_id IS NULL
)
ORDER BY l.name, age;
