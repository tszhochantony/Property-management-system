ALTER TABLE `announcement_record` ADD `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `announcement_content`;
