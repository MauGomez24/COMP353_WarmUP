SELECT
    SUM(
        CASE
            WHEN cm.is_minor = 0 AND p.amount <= 200 THEN p.amount
            WHEN cm.is_minor = 0 AND p.amount > 200 THEN 200
            ELSE 0
        END
    ) AS total_fees_paid,
    SUM(
        CASE
            WHEN cm.is_minor = 0 AND p.amount > 200 THEN p.amount - 200
            ELSE 0
        END
    ) AS total_donations
FROM payments p
JOIN club_members cm ON p.cm_id = cm.cm_id
WHERE cm.is_minor = 0
  AND p.memb_year BETWEEN 2020 AND 2024;
