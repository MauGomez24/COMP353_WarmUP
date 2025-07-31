-- query 10
SELECT
    p.first_name AS coach_first_name,
    p.last_name AS coach_last_name,
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
ORDER BY s.date ASC, s.time ASC;
