-- Query 12: Report on team formations for all locations
-- Period: From September 1st, 2023 to October 31st, 2023

DROP VIEW IF EXISTS view_team_formation_report_by_location;
CREATE VIEW view_team_formation_report_by_location AS
SELECT
    l.location_id,
    l.name AS location_name,
    COUNT(DISTINCT CASE WHEN s.type = 'train' THEN s.session_id END) AS total_training_sessions,
    COUNT(DISTINCT CASE WHEN s.type = 'train' THEN CONCAT(s.session_id, '_', tp.cm_id) END) AS total_training_players,
    COUNT(DISTINCT CASE WHEN s.type = 'game' THEN s.session_id END) AS total_game_sessions,
    COUNT(DISTINCT CASE WHEN s.type = 'game' THEN CONCAT(s.session_id, '_', tp.cm_id) END) AS total_game_players
FROM sessions s
JOIN locations l ON s.location_id = l.location_id
JOIN teams t ON s.session_id = t.session_id
JOIN team_players tp ON t.team_id = tp.team_id
WHERE s.date BETWEEN '2023-09-01' AND '2023-10-31'
GROUP BY l.location_id, l.name
HAVING total_game_sessions >= 4
ORDER BY total_game_sessions DESC;
