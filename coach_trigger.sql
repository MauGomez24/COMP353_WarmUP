-- can check if exists with SHOW TRIGGERS;

DELIMITER $$

CREATE TRIGGER validate_coach_id
BEFORE INSERT ON teams
FOR EACH ROW
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM personnel
    WHERE employee_id = NEW.coach_id AND role = 'coach'
  ) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'coach_id must reference an employee with role = coach';
  END IF;
END$$

DELIMITER ;
