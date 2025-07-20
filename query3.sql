#3
SELECT 
    l.name AS location_name,
    cm.cm_id AS membership_number,
    cm.first_name,
    cm.last_name,
    cm.age,
    cm.city,
    cm.province,
    CASE
        WHEN m.is_active = 1 THEN 'active'
        ELSE 'inactive'
    END AS status,
    COUNT(h.hobby) AS hobby_count
FROM club_members cm
JOIN family_members fm ON cm.fm_id = fm.fm_id
JOIN locations l ON fm.location_id = l.location_id
LEFT JOIN hobbies h ON cm.cm_id = h.cm_id
LEFT JOIN memberships m ON cm.cm_id = m.cm_id AND m.memb_year = 2024
GROUP BY cm.cm_id, l.name, m.is_active, cm.first_name, cm.last_name, cm.age, cm.city, cm.province
HAVING COUNT(h.hobby) >= 3
ORDER BY cm.age DESC, l.name ASC;


