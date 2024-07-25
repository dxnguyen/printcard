CREATE TABLE IF NOT EXISTS `#__requests` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`title` VARCHAR(300)  NOT NULL ,
`description` TEXT NULL ,
`requester_id` INT(11)  NOT NULL ,
`requester_deparment` INT(11)  NULL  DEFAULT 0,
`type_id` TEXT NULL ,
`status` VARCHAR(255)  NULL  DEFAULT "0",
`technician_id` INT(11)  NULL  DEFAULT 0,
`tech_department` INT(11)  NULL  DEFAULT 0,
`created_date` DATETIME NULL  DEFAULT NULL ,
`start_date` DATETIME NULL  DEFAULT NULL ,
`end_date` DATETIME NULL  DEFAULT NULL ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__requests_title` ON `#__requests`(`title`);

CREATE INDEX `#__requests_requester_id` ON `#__requests`(`requester_id`);

CREATE INDEX `#__requests_requester_deparment` ON `#__requests`(`requester_deparment`);

CREATE INDEX `#__requests_status` ON `#__requests`(`status`);

CREATE INDEX `#__requests_technician_id` ON `#__requests`(`technician_id`);

CREATE INDEX `#__requests_tech_department` ON `#__requests`(`tech_department`);

CREATE INDEX `#__requests_created_date` ON `#__requests`(`created_date`);

CREATE INDEX `#__requests_start_date` ON `#__requests`(`start_date`);

CREATE INDEX `#__requests_end_date` ON `#__requests`(`end_date`);


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Request','com_requests.request','{"special":{"dbtable":"#__requests","key":"id","type":"RequestTable","prefix":"Joomla\\\\Component\\\\Requests\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_requests\/forms\/request.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"description"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_requests.request')
) LIMIT 1;
