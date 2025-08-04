-- query 22
-- Insert a generated email into the email_log table for a scheduled training session
INSERT INTO email_log (
    email_id,
    sender_loc_id,
    receiver_cm_id,
    date,
    subject,
    body
)
VALUES (
    1002, -- Unique ID
    1, -- Location ID (sender)
    10, -- cm_id (receiver)
    CURDATE(),
    'Montreal Group A - 05-Aug-2025 6:00 PM training session',
    'Dear John Smith, you are assigned as setter. Head Coach: Sarah Lee (sarah@club.com). This is a training session held at 123 Volleyball Street.'
);
-- Retrieve and display the logged emails, most recent first
SELECT * FROM email_log ORDER BY date DESC;
