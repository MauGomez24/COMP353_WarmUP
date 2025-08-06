-- query 8
DROP VIEW IF EXISTS view_location_summary;
CREATE VIEW view_location_summary AS
SELECT 
    l.location_id,
    l.address,
    pc.city,
    pc.province,
    l.postal_code,
    l.phone,
    l.web_address,
    CASE WHEN l.is_head = 1 THEN 'Head' ELSE 'Branch' END AS type,
    l.max_capacity,
    (SELECT CONCAT(p.first_name, ' ', p.last_name)
     FROM personnel p
     WHERE p.location_id = l.location_id AND p.role = 'general manager'
     LIMIT 1) AS general_manager_name,
    (SELECT COUNT(*) 
     FROM club_members cm
     WHERE cm.location_id = l.location_id AND cm.is_minor = 1) AS minor_members,
    (SELECT COUNT(*) 
     FROM club_members cm
     WHERE cm.location_id = l.location_id AND cm.is_minor = 0) AS major_members,
    (SELECT COUNT(*) 
     FROM teams t
     WHERE t.location_id = l.location_id) AS number_of_teams
FROM 
    locations l
JOIN 
    postal_codes pc ON l.postal_code = pc.postal_code
ORDER BY 
    pc.province ASC,
    pc.city ASC;
