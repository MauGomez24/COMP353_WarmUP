USE ftc353_1;

/* 
    Query 16:
    This view generates a report of all members who have always been assigned to all positions (setter, libero, outside hitter, opposite hitter) at least once
*/

DROP VIEW IF EXISTS all_positions;
CREATE VIEW all_positions AS

WITH 
    setters AS (
        SELECT
            cm_id
        FROM team_players
        WHERE `role` = 'setter'
    ),

    liberos AS (
        SELECT
            cm_id
        FROM team_players
        WHERE `role` = 'libero'
    ),

    out_hit AS (
        SELECT
            cm_id
        FROM team_players
        WHERE `role` = 'outisde hitter'
    ),

    opp_hit AS (
        SELECT
            cm_id
        FROM team_players
        WHERE `role` = 'opposite hitter'
    ),

    all_roles AS (
        SELECT
            s.cm_id,
            cm.first_name,
            cm.last_name,
            cm.age,
            cm.phone,
            cm.email,
            loc.name as location_name
        FROM setters s
        INNER JOIN liberos l
            ON s.cm_id = l.cm_id
        INNER JOIN out_hit ot
            ON s.cm_id = ot.cm_id
        INNER JOIN opp_hit op
            ON s.cm_id = op.cm_id
        INNER JOIN club_members cm
            ON s.cm_id = cm.cm_id
        INNER JOIN locations loc
            ON cm.location_id = loc.location_id
    )

SELECT * FROM all_roles;