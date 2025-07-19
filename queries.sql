#1
SELECT l.address, l.city, l.province, l.postal_code, l.phone, l.web_address, IF(l.is_head = 1, 'Head', 'Branch') as type, l.max_capacity, CONCAT(p.first_name, ' ', p.last_name) AS general_manager, COUNT(DISTINCT p.employee_id) AS personnel_num, COUNT(DISTINCT c.cm_id) AS club_members_num
FROM locations l 
JOIN personnel p ON l.location_id = p.location_id
JOIN family_members f ON l.location_id = f.location_id
JOIN club_members c ON f.fm_id = c.fm_id
GROUP BY l.address, l.city, l.province, l.postal_code, l.phone, l.web_address, IF(l.is_head = 1, 'Head', 'Branch'), l.max_capacity, p.first_name, p.last_name
ORDER BY COUNT(DISTINCT c.cm_id) DESC;


#2
SELECT l.name, c.cm_id AS membership_num, c.first_name, c.last_name, c.age, c.city, c.province, IF(m.is_active = 1, 'Active', 'Inactive') AS status, lw.name AS workplace
FROM locations l
JOIN family_members f ON l.location_id = f.location_id
JOIN club_members c ON f.fm_id = c.fm_id
JOIN memberships m ON c.cm_id = m.cm_id
JOIN personnel p ON c.ssn = p.ssn 
JOIN locations lw ON p.location_id = lw.location_id
WHERE c.is_minor = 0
ORDER BY l.name ASC, lw.name ASC, c.age ASC;