/* 
	This query produces a report with the total number of club members for every age in all locations of the club.
    Results are displayed by ascending order of age
*/

USE ftc353_1;

-- getting a list of available locations
CREATE TEMPORARY TABLE loc_list AS (
	SELECT
		location_id,
		name,
		city,
		province
	FROM LOCATIONS
);

-- getting a list of all club_members and their respective locations
CREATE TEMPORARY TABLE memb_list AS (
	SELECT
		cm.cm_id,
        cm.age,
        fm.location_id
    FROM club_members cm
	INNER JOIN family_members fm
		ON cm.fm_id = fm.fm_id
);

-- joining the previous two tables and counting the number of members for each age
SELECT
	loc.location_id AS ID,
    loc.name AS location,
    loc.city,
    loc.province,
    memb.age,
    COUNT(DISTINCT memb.cm_id) AS num_of_members
FROM loc_list loc
INNER JOIN memb_list memb
	ON loc.location_id = memb.location_id
GROUP BY
	loc.location_id,
    loc.name,
    loc.city,
    loc.province,
    memb.age
ORDER BY memb.age ASC;

DROP TABLE loc_list;
DROP TABLE memb_list;