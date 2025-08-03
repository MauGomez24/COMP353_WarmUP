-- query 13

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
