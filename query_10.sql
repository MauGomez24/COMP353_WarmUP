-- query 10
DROP VIEW IF EXISTS view_team_formations_by_location_period;
CREATE VIEW view_team_formations_by_location_period AS

SELECT
	s.session_id,
    p.first_name AS coach_first_name,
    p.last_name AS coach_last_name,
    s.date AS session_date,
    s.time AS session_time,
    l.address AS session_address,
    s.type AS session_type,
    t.name AS team_name,
    CASE
        WHEN s.date <= CURDATE() THEN t.score
        ELSE NULL
    END AS team_score,
    cm.first_name AS player_first_name,
    cm.last_name AS player_last_name,
    tp.role AS player_role
FROM sessions s
JOIN locations l ON s.location_id = l.location_id
JOIN teams t ON s.session_id = t.session_id
JOIN personnel p ON t.coach_id = p.employee_id
JOIN team_players tp ON t.team_id = tp.team_id
JOIN club_members cm ON tp.cm_id = cm.cm_id
WHERE s.location_id = 1 AND s.date >= '2023-01-01' AND s.date < '2024-01-01'
ORDER BY s.date ASC, s.time ASC;