-- query 11

-- Insert payment records for club members 50 to 57
-- Members 50 to 55 have only one old payment from 2022 (making them eligible)
-- Members 56 and 57 have mixed years (for variety and test cases)

DROP VIEW IF EXISTS view_inactive_longterm_multilocation_members;
CREATE VIEW view_inactive_longterm_multilocation_members AS

SELECT DISTINCT 
    cm.cm_id,
    cm.first_name,
    cm.last_name
FROM
    club_members cm
JOIN memberships m ON cm.cm_id = m.cm_id
WHERE m.is_active = 0
  AND cm.cm_id IN (
      SELECT cm_id
      FROM payments
      GROUP BY cm_id
      HAVING YEAR(CURDATE()) - MIN(memb_year) >= 2
  )
  AND cm.cm_id IN (
      SELECT tp.cm_id
      FROM team_players tp
      JOIN teams t ON tp.team_id = t.team_id
      GROUP BY tp.cm_id
      HAVING COUNT(DISTINCT t.location_id) >= 2
  )
ORDER BY cm.cm_id;


-- Test data: insert payments for members 50–55
INSERT INTO payments (cm_id, memb_year, payment_date, amount, method) VALUES
(50, 2022, '2022-06-01', 99.99, 'cash'),
(51, 2022, '2022-07-01', 120.00, 'credit'),
(52, 2022, '2022-08-15', 110.50, 'debit'),
(53, 2022, '2022-06-22', 135.75, 'cash'),
(54, 2022, '2022-09-10', 142.30, 'credit'),
(55, 2022, '2022-05-30', 127.80, 'debit');
