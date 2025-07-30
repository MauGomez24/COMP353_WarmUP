USE ftc353_1;

/* 
    Query 19:
    This view generates a report of all volunteer personnel who are family members of at least one minor club member
*/

DROP VIEW IF EXISTS volunteer_emp;
CREATE VIEW volunteer_emp AS

WITH
    minor_members AS (
        SELECT
            fm_id,
            COUNT(cm_id) as num_minors
        FROM club_members
        WHERE is_minor = 1
        GROUP BY fm_id
    ),

    volunteers AS (
        SELECT
            p.first_name,
            p.last_name,
            mm.num_minors,
            p.phone,
            p.email,
            loc.name as location_name,
            p.role
        FROM family_members fm
        INNER JOIN minor_members mm
            ON fm.fm_id = mm.fm_id
        INNER JOIN personnel p
            ON fm.ssn = p.ssn
        INNER JOIN locations loc
            ON loc.location_id = p.location_id
        WHERE p.mandate = 'volunteer'
        ORDER BY
            location_name ASC,
            p.role ASC,
            p.first_name ASC,
            p.last_name ASC
    )

SELECT * FROM volunteers;