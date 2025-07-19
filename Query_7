USE ftc353_1;

SELECT
    -- Sum up the actual membership fees paid (max $200 per payment)
    SUM(
        CASE
            -- If member is major and paid <= $200, count full amount as fee
            WHEN cm.is_minor = 0 AND p.amount <= 200 THEN p.amount
            -- If member is major and paid more than $200, count only $200 as fee
            WHEN cm.is_minor = 0 AND p.amount > 200 THEN 200
            -- This case shouldn't occur due to the WHERE clause, but added for completeness
            ELSE 0
        END
    ) AS total_fees_paid,

    -- Sum up the excess amounts paid beyond the $200 fee (treated as donations)
    SUM(
        CASE
            -- If member is major and paid more than $200, count the excess as donation
            WHEN cm.is_minor = 0 AND p.amount > 200 THEN p.amount - 200
            -- All other cases don't contribute to donations
            ELSE 0
        END
    ) AS total_donations

-- Join club members with their payment records
FROM club_members cm
JOIN payments p ON cm.cm_id = p.cm_id

-- Filter only major members and payments made between 2020 and 2024
WHERE cm.is_minor = 0
  AND p.memb_year BETWEEN 2020 AND 2024;
