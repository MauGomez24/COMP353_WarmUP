-- Old Tables
USE ftc353_1;

DROP TABLE IF EXISTS email_log;
DROP TABLE IF EXISTS team_players;
DROP TABLE IF EXISTS teams;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS family_club_relations;
DROP TABLE IF EXISTS sec_fam_members;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS hobbies;
DROP TABLE IF EXISTS club_members;
DROP TABLE IF EXISTS family_members;
DROP TABLE IF EXISTS personnel_loc_hist;
DROP TABLE IF EXISTS personnel;
DROP TABLE IF EXISTS locations;

-- POSTAL CODES
CREATE TABLE postal_codes (
    postal_code VARCHAR(10) PRIMARY KEY,
    city VARCHAR(50),
    province VARCHAR(50)
);

-- LOCATIONS
CREATE TABLE locations (
    location_id INT PRIMARY KEY,
    is_head BOOLEAN,
    name VARCHAR(100),
    address VARCHAR(100),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    web_address VARCHAR(100),
    max_capacity INT,
	FOREIGN KEY (postal_code) REFERENCES postal_codes(postal_code)
);

-- PERSONNEL
CREATE TABLE personnel (
    employee_id INT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    date_of_birth DATE,
    ssn VARCHAR(15) NOT NULL UNIQUE,
    medicare_num VARCHAR(20) UNIQUE,
    phone VARCHAR(20),
    address VARCHAR(100),
    postal_code VARCHAR(10),
    email VARCHAR(100),
    role ENUM('administrator', 'captain', 'coach', 'assistant coach', 'general manager', 'deputy manager', 'treasurer', 'secretary', 'other'),
    mandate ENUM('volunteer', 'salaried'),
    location_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id),
	FOREIGN KEY (postal_code) REFERENCES postal_codes(postal_code)
);

-- PERSONNEL LOCATION HISTORY
CREATE TABLE personnel_loc_hist (
    employee_id INT,
    location_id INT,
    start_date DATE,
    end_date DATE,
    PRIMARY KEY (employee_id, location_id, start_date),
    FOREIGN KEY (employee_id) REFERENCES personnel(employee_id),
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
);

-- FAMILY MEMBERS
CREATE TABLE family_members (
    fm_id INT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    date_of_birth DATE,
    ssn VARCHAR(15) NOT NULL UNIQUE,
    medicare_num VARCHAR(20),
    phone VARCHAR(20),
    address VARCHAR(100),
    postal_code VARCHAR(10),
    email VARCHAR(100),
    location_id INT,
    relationship ENUM('father', 'mother', 'grandfather', 'grandmother', 'tutor', 'partner', 'friend', 'other'),
    FOREIGN KEY (location_id) REFERENCES locations(location_id),
	FOREIGN KEY (postal_code) REFERENCES postal_codes(postal_code)
);

-- CLUB MEMBERS
CREATE TABLE club_members (
    cm_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    age INT CHECK (age >= 11),
    is_minor BOOLEAN,
    date_of_birth DATE,
    height FLOAT,
    weight FLOAT,
    ssn VARCHAR(15) NOT NULL UNIQUE,
    medicare_num VARCHAR(20),
    phone VARCHAR(20),
    address VARCHAR(100),
    postal_code VARCHAR(10),
	gender CHAR(1),
    location_id INT,
    fm_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id),
    FOREIGN KEY (fm_id) REFERENCES family_members(fm_id),
	FOREIGN KEY (postal_code) REFERENCES postal_codes(postal_code),
	CHECK (
		(is_minor = 0 OR fm_id IS NOT NULL) AND -- either the member is an adult or they have a family member assigned
		(gender IN ('M', 'F'))
	)
);

-- HOBBIES
CREATE TABLE hobbies (
    cm_id INT,
    hobby VARCHAR(50),
    PRIMARY KEY (cm_id, hobby),
    FOREIGN KEY (cm_id) REFERENCES club_members(cm_id)
);

-- PAYMENTS
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT,
    cm_id INT,
    memb_year INT,
    payment_date DATE,
    amount DECIMAL (5, 2),
    method ENUM('credit', 'debit', 'cash'),
    PRIMARY KEY (payment_id)
    FOREIGN KEY (cm_id) REFERENCES club_members(cm_id)
);

-- New Tables

-- SECONDARY FAMILY MEMBERS
CREATE TABLE sec_fam_members (
    sfm_id INT PRIMARY KEY,
    fm_id INT UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    relationship ENUM('father', 'mother', 'grandfather', 'grandmother', 'tutor', 'partner', 'friend', 'other'),
    FOREIGN KEY (fm_id) REFERENCES family_members(fm_id)
);

-- FAMILY CLUB RELATIONS
CREATE TABLE family_club_relations (
    cm_id INT,
    fm_id INT,
    sfm_id INT,
    PRIMARY KEY (cm_id, fm_id),
    FOREIGN KEY (cm_id) REFERENCES club_members(cm_id),
    FOREIGN KEY (fm_id) REFERENCES family_members(fm_id),
    FOREIGN KEY (sfm_id) REFERENCES sec_fam_members(sfm_id),
    UNIQUE (cm_id)
);

-- SESSIONS
CREATE TABLE sessions (
    session_id INT PRIMARY KEY,
	type ENUM('train', 'game'),
    date DATE,
    time TIME,
	location_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
);

-- TEAMS
CREATE TABLE teams (
    team_id INT PRIMARY KEY,
    name VARCHAR(100),
    score INT,
    coach_id INT,
    location_id INT,
    session_id INT,
    FOREIGN KEY (coach_id) REFERENCES personnel(employee_id),
    FOREIGN KEY (location_id) REFERENCES locations(location_id),
    FOREIGN KEY (session_id) REFERENCES sessions(session_id)
);

-- TEAM PLAYERS
CREATE TABLE team_players (
    cm_id INT,
    team_id INT,
    role ENUM('goalkeeper', 'defender', 'midfielder', 'forward'),  
	PRIMARY KEY (cm_id, team_id),
    FOREIGN KEY (cm_id) REFERENCES club_members(cm_id),
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
);

-- EMAIL LOG
CREATE TABLE email_log (
    email_id INT PRIMARY KEY,
    sender_loc_id INT,
    receiver_cm_id INT,
    date DATE,
    subject VARCHAR(50),
    body VARCHAR(100),
    FOREIGN KEY (sender_loc_id) REFERENCES locations(location_id),
    FOREIGN KEY (receiver_cm_id) REFERENCES club_members(cm_id)
);