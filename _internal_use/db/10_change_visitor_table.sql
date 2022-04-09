ALTER TABLE visitor ADD status INT(1) NOT NULL DEFAULT '2' COMMENT '0 = disabled, 1 = enabled, 2 = temporary' AFTER record_id;
ALTER TABLE visitor ADD hash CHAR(128) NOT NULL AFTER email;
ALTER TABLE visitor CHANGE `eng_first_name` `eng_first_name` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE `eng_last_name` `eng_last_name` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL, CHANGE `id_no` `id_no` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
ALTER TABLE visitor CHANGE `timestamp` `access_date` DATE NOT NULL;
