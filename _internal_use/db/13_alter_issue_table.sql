ALTER TABLE `issue` ADD `raise_flag` INT(1) NOT NULL COMMENT '0 = raised by owners, 1 = raised by management' AFTER `issue_details`, ADD `raised_by` VARCHAR(128) NOT NULL AFTER `raise_flag`;
ALTER TABLE `issue` CHANGE `status` `cutoff_time` TIMESTAMP NOT NULL;
ALTER TABLE `issue` DROP `cutoff_time`;
ALTER TABLE `issue` ADD `cutoff_time` TIMESTAMP NOT NULL AFTER `raised_by`;
