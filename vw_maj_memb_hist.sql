USE ftc353_1;

/* 
    Query 14:
    This view generates a report of all members who have been part of the club since they were minors
*/

DROP VIEW IF EXISTS major_memb_hist;
CREATE VIEW major_memb_hist AS

-- getting the list of members
WITH majors AS (
    SELECT
        cm_id,
        age
    FROM club_members
    WHERE is_minor = 0
),

active_majors AS (
    SELECT
        mem.cm_id,
        maj.age,
        mem.is_active,
        MIN(mem.memb_year) OVER(PARTITION BY mem.cm_id) AS earliest_year,
        MAX(mem.memb_year) OVER(PARTITION BY mem.cm_id) AS latest_year,
        ROW_NUMBER() OVER(
            PARTITION BY mem.cm_id
            ORDER BY mem.memb_year DESC
        ) AS rn
    FROM memberships mem
    INNER JOIN majors maj
        ON mem.cm_id = maj.cm_id
),

age_calc AS (
    SELECT
        ac.cm_id,
        MIN(year(p.payment_date)) OVER(PARTITION BY p.cm_id) as start_date
    FROM active_majors ac
    INNER JOIN payments p
        ON ac.cm_id = p.cm_id
        AND ac.earliest_year = year(p.payment_date)
    WHERE
        ac.rn = 1
        AND ac.is_active = 1
        -- check: if current age - time in club < 18 then member has been here since they were a minor
        AND (ac.age - (ac.latest_year - ac.earliest_year)) < 18
),

final_info AS (
    SELECT
        cm.cm_id,
        cm.first_name,
        cm.last_name,
        ac.start_date,
        cm.age,
        cm.phone,
        cm.email,
        loc.name as location_name
    FROM club_members cm
    INNER JOIN age_calc ac
        ON cm.cm_id = ac.cm_id
    INNER JOIN locations loc
        ON cm.location_id = loc.location_id
    GROUP BY
        cm.cm_id,
        cm.first_name,
        cm.last_name,
        ac.start_date,
        cm.age,
        cm.phone,
        cm.email,
        loc.name
)

SELECT * FROM final_info
ORDER BY location_name ASC, age ASC;