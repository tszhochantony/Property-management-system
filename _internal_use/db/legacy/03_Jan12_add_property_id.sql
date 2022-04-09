ALTER TABLE `resident` ADD `property_id` INT(10) NULL AFTER `mobile_phone`;
ALTER TABLE `resident` ADD CONSTRAINT `resident_fk` FOREIGN KEY (`property_id`) REFERENCES `property`(`row_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
