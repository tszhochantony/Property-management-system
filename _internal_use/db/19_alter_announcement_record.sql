ALTER TABLE `announcement_record` ADD `expire_date` DATE NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `timestamp`;
