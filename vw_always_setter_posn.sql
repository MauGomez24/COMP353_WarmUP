USE ftc353_1;

/* 
    Query 15:
    This view generates a report of all members who have always been assigned the setter position in all the matches they have played and none other role
*/

DROP VIEW IF EXISTS always_setter_posn;
CREATE VIEW always_setter_posn AS

WITH
    setters_only AS (
        SELECT cm_id
        FROM team_players
        GROUP BY cm_id
        HAVING COUNT(DISTINCT CASE WHEN role != 'setter' THEN role END) = 0
    ),

    setters_info AS (
        SELECT
            cm.cm_id,
            cm.first_name,
            cm.last_name,
            cm.age,
            cm.phone,
            cm.email,
            loc.name as location_name
        FROM setters_only st
        INNER JOIN club_members cm
            ON st.cm_id = cm.cm_id
        INNER JOIN locations loc
            ON cm.location_id = loc.location_id
    )

SELECT * FROM setters_info
ORDER BY location_name ASC, cm_id ASC;