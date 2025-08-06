USE ftc353_1;

/* 
    Query 17:
    This view generates a report of all family members who are assigned to active club_members and who are also head coaches
*/

DROP VIEW IF EXISTS fam_coach;
CREATE VIEW fam_coach AS

WITH
    membership_list AS (
        SELECT
            cm_id,
            is_active,
            ROW_NUMBER() OVER(
                PARTITION BY cm_id
                ORDER BY memb_year ASC
            ) AS rn
        FROM memberships
    ),

    fam_active_members AS (
        SELECT
            ml.cm_id,
            cm.fm_id,
            fm.first_name,
            fm.last_name,
            fm.phone,
            fm.ssn
        FROM membership_list ml
        INNER JOIN club_members cm
            ON ml.cm_id = cm.cm_id
        INNER JOIN family_members fm
            ON cm.fm_id = fm.fm_id
        WHERE ml.rn = 1
            AND ml.is_active = 1
    ),

    fam_coaches AS (
        SELECT
            DISTINCT fam.first_name,
            fam.last_name,
            fam.phone
        FROM fam_active_members fam
        INNER JOIN personnel p
            ON fam.ssn = p.ssn
        INNER JOIN teams t
            ON t.coach_id = p.employee_id
    )

SELECT * FROM fam_coaches;