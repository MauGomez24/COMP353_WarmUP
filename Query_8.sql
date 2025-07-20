use ftc353_1

WITH latest_membership AS (
  SELECT
    cm_id,
    memb_year,
    fee,
    total_paid,
    donation_amt,
    is_active,
    ROW_NUMBER() OVER (
      PARTITION BY cm_id
      ORDER BY memb_year DESC
    ) AS rn
  FROM memberships
)
SELECT
  l.name AS location_name,
  cm.cm_id,
  cm.first_name,
  cm.last_name,
  cm.age,
  cm.city,
  cm.province,
  (m.fee - m.total_paid) AS amount_due
FROM latest_membership m
JOIN club_members cm ON m.cm_id = cm.cm_id
JOIN family_members fm ON cm.fm_id = fm.fm_id
JOIN locations l ON fm.location_id = l.location_id
WHERE m.is_active = 0
  AND m.rn = 1
ORDER BY l.name ASC, cm.age ASC;
