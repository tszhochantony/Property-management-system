CREATE TABLE resident_qr_code (
    email VARCHAR(128) NOT NULL,
    qr_code VARCHAR(128) NOT NULL,
    PRIMARY KEY (email),
    FOREIGN KEY (email) REFERENCES resident(email)
);
