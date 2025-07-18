USE ftc353_1;

/* 
    This is a non-materialized view which is simply a stored SELECT statement.
    Whenever SELECT FROM memberships is executed, the table will be recalculated,
    allowing us to have up to date info on the status of active and inactive memberships
*/

DROP VIEW IF EXISTS memberships;
CREATE VIEW memberships AS
WITH
	expanded_pay_info AS (
		SELECT
			p.cm_id,
			p.memb_year,
			p.payment_date,
			p.amount,
			CASE
				WHEN cm.is_minor = 1 THEN 100 ELSE 200
			END AS memb_cost
		FROM payments p
		INNER JOIN club_members cm
			ON p.cm_id = cm.cm_id
	),
    numbered_payments AS (
		SELECT
			cm_id,
			memb_year,
            payment_date,
            amount,
            memb_cost,
            ROW_NUMBER() OVER(
				PARTITION BY cm_id, memb_year
                ORDER BY payment_date ASC
            ) AS rn
        FROM expanded_pay_info
    ),
	aggregated_payments AS (
	  SELECT
		cm_id,
		memb_year,
		COUNT(*) AS num_of_payments,
		SUM(amount) AS total_paid,
		-- since the deadline for next year's memebership is Dec 31st of the current year
        -- we will only consider payments made on or before that date
		SUM(
		  CASE
			WHEN payment_date <= STR_TO_DATE(CONCAT(memb_year-1, '-12-31'), '%Y-%m-%d')
			THEN amount
			ELSE 0
		  END
		) AS paid_by_deadline,
        memb_cost
	  FROM numbered_payments
      WHERE rn <= 4 -- we take only the first 4 payments into account
	  GROUP BY cm_id, memb_year
	),
    calculated AS (
		SELECT
			cm_id,
            memb_year,
            memb_cost AS fee,
            total_paid,
            /*
				If a member has reached the installment limit of 4 payments and their membership has not been paid in full, 
                then whatever they paid will be considered a donation to the club
            */
            CASE
				WHEN (memb_cost - total_paid) > 0 AND num_of_payments < 4 THEN 0 -- the member has not yet paid the membership in full but has a few installments left
                WHEN (memb_cost - total_paid) > 0 AND num_of_payments = 4 THEN total_paid -- member has not paid fee in full and has reached instalment limit
                ELSE CAST((memb_cost - total_paid) * -1 AS DECIMAL(5,2)) -- member has paid fee in full
            END AS donation_amt,
            num_of_payments
        FROM aggregated_payments
    ),
    final_status AS (
		SELECT
			cm_id,
            memb_year,
            fee,
            total_paid,
            donation_amt,
            CASE
				WHEN total_paid < fee THEN 0 ELSE 1
            END AS is_active
        FROM calculated
    )

SELECT * FROM final_status;
