ALTER TABLE `resident` CHANGE `is_valid` `status` INT(1) NOT NULL DEFAULT '2' COMMENT '0 = disabled, 1 = enabled, 2 = temp';
ALTER TABLE `resident` CHANGE `email` `email` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `username`;
ALTER TABLE `resident` DROP `username`;
ALTER TABLE `resident` CHANGE `email` `email` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `resident` ADD PRIMARY KEY( `email`);
ALTER TABLE `resident` ADD `hashed_email` CHAR(128) NOT NULL AFTER `email`;
ALTER TABLE `resident` CHANGE `password` `password` CHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL, CHANGE `eng_first_name` `eng_first_name` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL, CHANGE `eng_last_name` `eng_last_name` VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
