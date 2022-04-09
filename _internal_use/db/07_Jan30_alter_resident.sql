ALTER TABLE resident DROP is_owner;
ALTER TABLE resident ADD lang CHAR(2) NOT NULL DEFAULT 'zh' AFTER property_id;
