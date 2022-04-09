ALTER TABLE `building` CHANGE `building_name` `chi_building_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `building` ADD `eng_building_name` VARCHAR(100) NOT NULL DEFAULT 'N/A' AFTER `chi_building_name`;
