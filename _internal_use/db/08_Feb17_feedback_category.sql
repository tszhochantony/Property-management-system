ALTER TABLE feedback_category ADD status INT(1) NOT NULL DEFAULT 1 COMMENT '0 = disabled, 1 = enabled' FIRST;
