USE ftc353_1;
DROP TABLE IF EXISTS hobbies;
DROP TABLE IF EXISTS club_members;
DROP TABLE IF EXISTS family_members;
DROP TABLE IF EXISTS personnel_loc_hist;
DROP TABLE IF EXISTS personnel;
DROP TABLE IF EXISTS locations;

-- LOCATIONS
CREATE TABLE locations (
    location_id INT PRIMARY KEY,
    is_head BOOLEAN,
    name VARCHAR(100),
    address VARCHAR(100),
    city VARCHAR(50),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    web_address VARCHAR(100),
    max_capacity INT
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
    city VARCHAR(50),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    email VARCHAR(100),
    role ENUM('administrator', 'captain', 'coach', 'assistant coach', 'general manager', 'deputy manager', 'treasurer', 'secreatary', 'other'),
    mandate ENUM('volunteer', 'salaried'),
    location_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
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
    city VARCHAR(50),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    email VARCHAR(100),
    location_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
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
    city VARCHAR(50),
    province VARCHAR(50),
    postal_code VARCHAR(10),
    fm_id INT,
    FOREIGN KEY (fm_id) REFERENCES family_members(fm_id)
);

-- HOBBIES
CREATE TABLE hobbies (
    cm_id INT,
    hobby VARCHAR(50),
    PRIMARY KEY (cm_id, hobby),
    FOREIGN KEY (cm_id) REFERENCES club_members(cm_id)
);
