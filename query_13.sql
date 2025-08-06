-- query 13

-- Delete any existing data for the dummy team_id = 999 to avoid duplicate entry errors
DELETE FROM team_players WHERE team_id = 999;
DELETE FROM teams WHERE team_id = 999;

-- Insert a new team called 'Phantoms' that is not assigned to any session (session_id = NULL)
INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
VALUES (999, 'Phantoms', NULL, 18, 1, NULL);

-- Assign three club members (IDs 49, 50, 51) to this team
INSERT INTO team_players (cm_id, team_id, role) VALUES
(49, 999, 'setter'),
(50, 999, 'libero'),
(51, 999, 'middle blocker');

-- Create view to list all active members who are not assigned to any team
DROP VIEW IF EXISTS view_active_members_without_team_assignments;
CREATE VIEW view_active_members_without_team_assignments AS
SELECT 
    cm.cm_id,
    cm.first_name,
    cm.last_name,
    cm.age,
    cm.phone,
    cm.email,
    l.name AS location_name
FROM club_members cm
JOIN memberships m ON cm.cm_id = m.cm_id AND m.is_active = 1
JOIN locations l ON cm.location_id = l.location_id
WHERE cm.cm_id NOT IN (
    SELECT DISTINCT cm_id
    FROM team_players
)
ORDER BY l.name ASC, cm.age ASC;
