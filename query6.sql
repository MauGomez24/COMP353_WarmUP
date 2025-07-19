USE ftc353_1;

-- getting a list of all club_members who are also family members
CREATE TEMPORARY TABLE major_memb AS (
	SELECT
		cm.cm_id,
        fm.fm_id,
		cm.first_name,
		cm.last_name,
        fm.relationship
	FROM club_members cm
    -- we perform the join on SSN since that indicates they are the same person
	INNER JOIN family_members fm
		ON cm.SSN = fm.SSN
	WHERE cm.is_minor = 0
);

-- getting the most recent status for all club memberships
/* 
    Since a member has a different membership that the system keeps track of each year,
    we will only get the most recent membership status for each member
*/
CREATE TEMPORARY TABLE most_recent_memberships AS (
	SELECT
		cm_id,
		is_active,
        -- we partition the table on cm_id, then order in decreasing year value
        ROW_NUMBER() OVER(
			PARTITION BY cm_id
            ORDER BY memb_year DESC
        ) AS rn
    FROM memberships
);

SELECT
	mm.first_name AS major_fname,
    mm.last_name AS major_lname,
    mm.relationship,
	c.cm_id AS minor_id,
    c.first_name AS minor_fname,
    c.last_name AS minor_lname,
    c.SSN AS minor_SSN,
    c.medicare_num AS minor_medicare_num,
    c.phone AS minor_phone_num,
    c.address AS minor_address,
    c.city AS minor_city,
    c.postal_code AS minor_postal_code,
    CASE
		WHEN m.is_active = 1 THEN 'active' ELSE 'inactive'
    END AS minor_status
FROM club_members c
INNER JOIN most_recent_memberships m
	ON c.cm_id = m.cm_id
INNER JOIN major_memb mm
	ON mm.fm_id = c.fm_id
WHERE m.rn = 1; -- we pick the most recent entry since they are ordered by decreasing year value
    
DROP TABLE major_memb;
DROP TABLE most_recent_memberships;