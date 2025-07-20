USE ftc353_1;

SELECT
    SUM(m.total_paid) AS total_fees_paid,
    SUM(m.donation_amt) AS total_donations
FROM club_members c
INNER JOIN memberships m
    ON m.cm_id = c.cm_id
WHERE c.is_minor = 0 AND m.memb_year BETWEEN 2020 AND 2024
