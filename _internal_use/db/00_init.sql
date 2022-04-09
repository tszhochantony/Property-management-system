CREATE TABLE building (
    building_id varchar(20) NOT NULL,
    chi_building_name varchar(100) NOT NULL,
    eng_building_name varchar(100) NOT NULL,
    PRIMARY KEY (building_id)
);

CREATE TABLE property (
    property_id int(10) NOT NULL AUTO_INCREMENT,
    building_id varchar(20),
    floor varchar(5),
    room_no varchar(5),
    PRIMARY KEY (property_id),
    FOREIGN KEY (building_id) REFERENCES building(building_id)
);

CREATE TABLE resident (
    status int(1) NOT NULL DEFAULT 2 COMMENT '0 = disabled, 1 = enabled, 2 = temporary',
    email varchar(128) NOT NULL,
    hashed_email char(128) NOT NULL,
    password char(128) NOT NULL,
    eng_first_name varchar(40) NOT NULL,
    eng_last_name varchar(40) NOT NULL,
    chi_first_name varchar(6) NULL,
    chi_last_name varchar(6) NULL,
    mobile_phone char(8) NULL,
    property_id int(10) NULL,
    is_owner int(1) NOT NULL DEFAULT 0 COMMENT '0 = false, 1 = true',
    PRIMARY KEY (email),
    FOREIGN KEY (property_id) REFERENCES property(property_id)
);

CREATE TABLE owner (
    property_id int(10) NOT NULL,
    user_email varchar(128) NOT NULL,
    PRIMARY KEY (property_id),
    FOREIGN KEY (property_id) REFERENCES property(property_id),
    FOREIGN KEY (user_email) REFERENCES resident(email)
);

CREATE TABLE issue (
    issue_id int(10) NOT NULL AUTO_INCREMENT,
    issue_title varchar(128) NOT NULL,
    issue_details varchar(1024) NOT NULL,
    status int(1) NOT NULL DEFAULT 1 COMMENT '0 = voting closed, 1 = accept for voting',
    PRIMARY KEY (issue_id)
);

CREATE TABLE issue_choice (
    issue_id int(10) NOT NULL,
    choice_id varchar(5) NOT NULL,
    choice_chi_desc varchar(50) NOT NULL,
    choice_eng_desc varchar(50) NOT NULL,
    PRIMARY KEY (issue_id, choice_id),
    FOREIGN KEY (issue_id) REFERENCES issue(issue_id)
);

CREATE INDEX issue_choice_index ON issue_choice (choice_id);

CREATE TABLE voting_record (
    issue_id int(10) NOT NULL,
    owner_email varchar(128) NOT NULL,
    owner_choice varchar(5) NOT NULL,
    PRIMARY KEY (issue_id, owner_email),
    FOREIGN KEY (issue_id) REFERENCES issue(issue_id),
    FOREIGN KEY (owner_email) REFERENCES resident(email),
    FOREIGN KEY (owner_choice) REFERENCES issue_choice(choice_id)
);

CREATE TABLE feedback_category (
    category_id char(5) NOT NULL,
    category_chi_name varchar(20) NOT NULL,
    category_eng_name varchar(40) NOT NULL,
    PRIMARY KEY (category_id)
);

CREATE TABLE feedback (
    record_id int(10) NOT NULL AUTO_INCREMENT,
    user_email varchar(128) NOT NULL,
    category_id char(5) NOT NULL,
    record_details varchar(1024) NOT NULL,
    status int(1) NOT NULL DEFAULT 0 COMMENT '0 = submitted, 1 = case transferred, 2 = file closed',
    timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (record_id),
    FOREIGN KEY (user_email) REFERENCES resident(email),
    FOREIGN KEY (category_id) REFERENCES feedback_category(category_id)
);

CREATE TABLE department (
    department_id varchar(10) NOT NULL,
    department_chi_name varchar(20) NOT NULL,
    department_eng_name varchar(40) NOT NULL,
    PRIMARY KEY (department_id)
);

CREATE TABLE feedback_referral (
    record_id int(10) NOT NULL,
    department_id varchar(10) NOT NULL,
    timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (record_id),
    FOREIGN KEY (record_id) REFERENCES feedback(record_id),
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

CREATE TABLE staff_position (
    position_id varchar(10) NOT NULL,
    position_chi_name varchar(20) NOT NULL,
    position_eng_name varchar(40) NOT nULL,
    department_id varchar(10) NOT NULL,
    PRIMARY KEY (position_id),
    FOREIGN KEY (department_id) REFERENCES department(department_id)
);

CREATE TABLE staff (
    status int(1) NOT NULL DEFAULT 1 COMMENT '0 = disabled, 1 = enabled',
    staff_id varchar(128) NOT NULL,
    password char(128) NOT NULL,
    eng_first_name varchar(40) NOT NULL,
    eng_last_name varchar(40) NOT NULL,
    chi_first_name varchar(6) NULL,
    chi_last_name varchar(6) NULL,
    mobile_phone char(8) NULL,
    address varchar(255) NOT NULL,
    position_id varchar(10) NOT NULL,
    PRIMARY KEY (staff_id),
    FOREIGN KEY (position_id) REFERENCES staff_position(position_id)
);

CREATE TABLE feedback_response (
    record_id int(10) NOT NULL,
    staff_id varchar(128) NOT NULL,
    response varchar(1024) NOT NULL,
    timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (record_id),
    FOREIGN KEY (record_id) REFERENCES feedback(record_id),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);

CREATE TABLE announcement_record (
    record_id int(10) NOT NULL AUTO_INCREMENT,
    staff_id varchar(128) NOT NULL,
    announcement_title varchar(128) NOT NULL,
    announcement_content varchar(1024) NOT NULL,
    PRIMARY KEY (record_id),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);

CREATE TABLE visitor (
    record_id int(10) NOT NULL AUTO_INCREMENT,
    email varchar(128) NOT NULL,
    resident_email varchar(128) NULL,
    staff_id varchar(128) NULL,
    eng_first_name varchar(40) NOT NULL,
    eng_last_name varchar(40) NOT NULL,
    id_no varchar(10) NOT NULL,
    timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (record_id),
    FOREIGN KEY (resident_email) REFERENCES resident(email),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);

CREATE TABLE qr_code (
    record_id int(10) NOT NULL,
    code char(128) NOT NULL,
    status int(1) NOT NULL DEFAULT 1 COMMENT '0 = unavailable, 1 = available',
    access_time timestamp NULL,
    PRIMARY KEY (record_id),
    FOREIGN KEY (record_id) REFERENCES visitor(record_id)
);

INSERT INTO department (department_id, department_chi_name, department_eng_name) VALUES ('admin', '系統管理員', 'System Administrator');
INSERT INTO staff_position (position_id, position_chi_name, position_eng_name, department_id) VALUES ('admin', '系統管理員', 'System Administrator', 'admin');
INSERT INTO staff (staff_id, password, eng_first_name, eng_last_name, address, position_id) VALUES ('admin', 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413', 'Admin', 'Admin', 'Administrator', 'admin');
