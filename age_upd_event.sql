-- reminder to enable event scheduler with
-- SET GLOBAL event_scheduler = ON;

CREATE EVENT update_age_and_minor_status
ON SCHEDULE EVERY 1 DAY
STARTS '2025-08-04 00:00:00'
DO
  UPDATE club_members
  SET 
    age = TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()),
    is_minor = IF(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18, TRUE, FALSE);
