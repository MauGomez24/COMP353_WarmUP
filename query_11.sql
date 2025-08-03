-- query 11

SELECT DISTINCT 
    cm.cm_id,
    cm.first_name,
    cm.last_name
FROM
    club_members cm
JOIN memberships m ON cm.cm_id = m.cm_id
WHERE m.is_active = 0
  AND cm.cm_id IN (
      SELECT cm_id
      FROM payments
      GROUP BY cm_id
      HAVING YEAR(CURDATE()) - MIN(memb_year) >= 2
  )
  AND cm.cm_id IN (
      SELECT tp.cm_id
      FROM team_players tp
      JOIN teams t ON tp.team_id = t.team_id
      GROUP BY tp.cm_id
      HAVING COUNT(DISTINCT t.location_id) >= 2
  )
ORDER BY cm.cm_id;