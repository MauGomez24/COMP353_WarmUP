#4
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
    END AS status
FROM club_members cm
JOIN family_members fm ON cm.fm_id = fm.fm_id
JOIN locations l ON fm.location_id = l.location_id
LEFT JOIN hobbies h ON cm.cm_id = h.cm_id
LEFT JOIN memberships m ON cm.cm_id = m.cm_id AND m.memb_year = 2024
WHERE h.cm_id IS NULL
ORDER BY l.name ASC, cm.age ASC;
