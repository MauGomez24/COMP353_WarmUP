-- query 20
/* The trigger prevent_conflicting_assignments helps enforce the rule that a player
 cannot be assigned to two formation sessions on the same day that are less than three
 hours apart. This ensures that scheduling conflicts are automatically detected and
 blocked before data is inserted into the database. The main benefits of this trigger 
 are that it enforces the time conflict rule consistently, prevents invalid or overlapping
 player assignments, and reduces human error. It also ensures data integrity across the system 
 and satisfies one of the key project requirements without needing manual checks or 
 application-side validations.
*/
DROP TRIGGER IF EXISTS prevent_conflicting_assignments;

DELIMITER $$

CREATE TRIGGER prevent_conflicting_assignments
BEFORE INSERT ON team_players
FOR EACH ROW
BEGIN
  DECLARE new_session_date DATE;
  DECLARE new_session_time TIME;

  -- Get the date and time of the new session for the team being inserted
  SELECT s.date, s.time
  INTO new_session_date, new_session_time
  FROM teams t
  JOIN sessions s ON t.session_id = s.session_id
  WHERE t.team_id = NEW.team_id;

  -- Check for time conflicts with the same member
  IF EXISTS (
    SELECT 1
    FROM team_players tp
    JOIN teams t2 ON tp.team_id = t2.team_id
    JOIN sessions s2 ON t2.session_id = s2.session_id
    WHERE tp.cm_id = NEW.cm_id
      AND s2.date = new_session_date
      AND ABS(TIMESTAMPDIFF(MINUTE, s2.time, new_session_time)) < 180
  ) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Player already assigned to another session less than 3 hours apart on the same day.';
  END IF;
END$$

DELIMITER ;
