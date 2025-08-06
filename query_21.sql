-- query 21
/* To demonstrate system integrity, we tested assigning a player to two 
sessions on the same day less than three hours apart. The system correctly 
blocked the second assignment using the trigger, confirming that scheduling r
ules are enforced and data remains consistent.*/

INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
VALUES
-- Attempt to assign personnel that is not coach to the team
(2001, 'Team A', NULL, 1, 1, 1);

INSERT INTO sessions (session_id, type, date, time, location_id) 
VALUES
(1001, 'train', '2025-08-05', '10:00:00', 1),
(1002, 'train', '2025-08-05', '11:30:00', 1); -- Only 1.5 hours later

INSERT INTO teams (team_id, name, score, coach_id, location_id, session_id)
VALUES
(2001, 'Team A', NULL, 62, 1, 1001),
(2002, 'Team B', NULL, 62, 1, 1002);

INSERT INTO team_players (cm_id, team_id, role)
VALUES (10, 2001, 'setter');

-- Attempt to assign the same player to the second session (Team B)
-- This should fail due to the trigger preventing assignments less than 3 hours apart
INSERT INTO team_players (cm_id, team_id, role)
VALUES (10, 2002, 'libero');