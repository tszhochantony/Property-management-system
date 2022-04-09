CREATE TABLE `fyp`.`feedback` (
    `record_id` INT(10) NOT NULL AUTO_INCREMENT ,
    `user_email` VARCHAR(128) NOT NULL ,
    `record_details` VARCHAR(1024) NOT NULL ,
    `status` INT(1) NOT NULL DEFAULT '0' COMMENT '0 = submitted, 1 = forwarded, 2 = case closed, 9 = cancelled' ,
    `category_id` CHAR(5) NOT NULL , PRIMARY KEY (`record_id`)
) ENGINE = InnoDB;

ALTER TABLE `feedback` CHANGE `category_id` `category_id` CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL AFTER `user_email`;

CREATE TABLE `fyp`.`feedback_category` (
    `category_id` CHAR(5) NOT NULL ,
    `category_chi_name` VARCHAR(20) NOT NULL ,
    `category_eng_name` INT(40) NOT NULL ,
    PRIMARY KEY (`category_id`)
) ENGINE = InnoDB;

ALTER TABLE `feedback` ADD CONSTRAINT `feedback_fk1` FOREIGN KEY (`category_id`) REFERENCES `feedback_category`(`category_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
