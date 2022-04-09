CREATE TABLE announcement_recipient (
    record_id int(10) NOT NULL,
    recipient varchar(20) NOT NULL,
    PRIMARY KEY (record_id, recipient),
    FOREIGN KEY (record_id) REFERENCES announcement_record(record_id)
);
