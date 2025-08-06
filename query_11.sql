-- query 11
-- Insert payment records for club members 50 to 57
-- Members 50 to 55 have only one old payment from 2022 (making them eligible for 2+ years membership)
-- Members 56 and 57 have mixed years (for variety and test cases)

DROP VIEW IF EXISTS view_inactive_longterm_multilocation_members;
CREATE VIEW view_inactive_longterm_multilocation_members AS

INSERT INTO payments (cm_id, memb_year, payment_date, amount, method) VALUES
(50, 2022, '2022-06-01', 99.99, 'cash'),
(51, 2022, '2022-07-01', 120.00, 'credit'),
(52, 2022, '2022-08-15', 110.50, 'debit'),
(53, 2022, '2022-06-22', 135.75, 'cash'),
(54, 2022, '2022-09-10', 142.30, 'credit'),
(55, 2022, '2022-05-30', 127.80, 'debit'),

-- mixed years
(56, 2022, '2022-05-15', 100.00, 'credit'),
(56, 2024, '2024-06-20', 150.00, 'debit'),
(57, 2021, '2021-03-01', 120.00, 'cash'),
(57, 2023, '2023-07-01', 130.00, 'credit');




SELECT 
    cm.cm_id,
    cm.first_name,
    cm.last_name
FROM 
    club_members cm
WHERE cm.cm_id NOT IN (
    SELECT cm_id FROM team_players
)
AND cm.cm_id IN (
    SELECT cm_id
    FROM payments
    GROUP BY cm_id
    HAVING YEAR(CURDATE()) - MIN(memb_year) >= 2
)
ORDER BY cm.cm_id;
