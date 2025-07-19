-- Step 1: Create a temporary table to get the most recent membership status for each member
CREATE TEMPORARY TABLE most_recent_memberships AS (
	SELECT
		cm_id,
		year,
		is_active,
		-- Assigns row numbers, partitioned by cm_id and ordered by year (most recent first)
		ROW_NUMBER() OVER(
			PARTITION BY cm_id
			ORDER BY year DESC
		) AS rn
	FROM memberships
);

-- Step 2: Create a temporary table to sum all 2024 payments for each member
CREATE TEMPORARY TABLE payments_2024 AS (
	SELECT
		cm_id,
		SUM(amount) AS total_paid
	FROM payments
	WHERE memb_year = 2024
	GROUP BY cm_id
);

-- Step 3: Select all inactive members along with the amount they owe and their current location
SELECT
	l.name AS location_name,  -- Name of the associated location
	c.cm_id AS membership_number,
	c.first_name,
	c.last_name,
	c.age,
	c.city,
	c.province,
	
	-- Calculate amount due based on whether the member is a minor or major
	CASE
		WHEN c.is_minor = 1 THEN 100 - IFNULL(p.total_paid, 0)
		ELSE 200 - IFNULL(p.total_paid, 0)
	END AS amount_due

FROM club_members c

-- Join with most recent membership table to get their current (latest) activity status
JOIN most_recent_memberships mrm ON c.cm_id = mrm.cm_id

-- Join with total payments for 2024 (can be NULL if no payments made)
LEFT JOIN payments_2024 p ON c.cm_id = p.cm_id

-- Join with family members to find their associated location
LEFT JOIN family_members fm ON c.fm_id = fm.fm_id

-- Join with locations to get the location name
LEFT JOIN locations l ON fm.location_id = l.location_id

-- Only consider the most recent membership row (rn = 1)
-- and filter for inactive members
WHERE mrm.rn = 1
  AND mrm.is_active = 0

-- Sort results by location name (A–Z), then by member age (youngest to oldest)
ORDER BY l.name ASC, c.age ASC;

-- Step 4: Clean up temporary tables
DROP TEMPORARY TABLE most_recent_memberships;
DROP TEMPORARY TABLE payments_2024;
