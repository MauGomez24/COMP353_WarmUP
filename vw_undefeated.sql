USE ftc353_1;

/* 
    Query 18:
    This view generates a report of all active club members who have never lost a game they where they played
*/

DROP VIEW IF EXISTS undefeated;
CREATE VIEW undefeated AS

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

    active_members AS (
        SELECT
            cm_id
        FROM membership_list
        WHERE rn = 1
    ),

    matches AS (
        SELECT
            t.team_id,
            t.session_id,
            ROW_NUMBER() OVER(
                PARTITION BY t.session_id
                ORDER BY t.score DESC
            ) AS rn
        FROM teams t
        INNER JOIN sessions s
            ON s.session_id = t.session_id
        WHERE s.type = 'game'
    ),

    team_results AS (
        SELECT
            team_id,
            SUM(CASE WHEN rn = 1 THEN 1 ELSE 0 END) as wins,
            SUM(CASE WHEN rn = 2 THEN 1 ELSE 0 END) as losses
        FROM matches
        GROUP BY team_id
    ),

    undefeated_members AS (
        SELECT
            tp.cm_id
        FROM team_results tr
        INNER JOIN team_players tp
            ON tr.team_id = tp.team_id
        INNER JOIN active_members am
            ON tp.cm_id = am.cm_id
        WHERE tr.losses = 0
    ),

    undefeated_members_info AS (
        SELECT
            cm.cm_id,
            cm.first_name,
            cm.last_name,
            cm.phone,
            cm.email,
            loc.name as location_name
        FROM undefeated_members um
        INNER JOIN club_members cm
            ON um.cm_id = cm.cm_id
        INNER JOIN locations loc
            ON cm.location_id = loc.location_id
    )

SELECT * FROM undefeated_members_info;