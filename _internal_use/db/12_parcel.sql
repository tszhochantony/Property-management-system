CREATE TABLE parcel (
    row_id int(10) NOT NULL AUTO_INCREMENT,
    resident_email varchar(128) NOT NULL,
    tracking_num varchar(20) NULL,
    ref_num char(12) NOT NULL,
    status int(1) NOT NULL DEFAULT 0 COMMENT '0 = keeping, 1 = returned',
    PRIMARY KEY (row_id),
    FOREIGN KEY (resident_email) REFERENCES resident(email)
);
