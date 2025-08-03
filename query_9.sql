-- query 9
SELECT
	fcr.fm_id,
    sfm.first_name AS sfm_first_name,
    sfm.last_name AS sfm_last_name,
    sfm.phone AS sfm_phone,
    cm.cm_id,
    cm.first_name AS cm_first_name,
    cm.last_name AS cm_last_name,
    cm.date_of_birth,
    cm.ssn,
    cm.medicare_num,
    cm.phone AS cm_phone,
    cm.address,
    pc.city,
    pc.province,
    cm.postal_code,
    sfm.relationship AS relationship_with_sfm
FROM sec_fam_members sfm
JOIN family_club_relations fcr ON sfm.sfm_id = fcr.sfm_id AND sfm.fm_id = fcr.fm_id
JOIN club_members cm ON fcr.cm_id = cm.cm_id
JOIN postal_codes pc ON cm.postal_code = pc.postal_code;
