CREATE TABLE IF NOT EXISTS `#__students` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`masv` BIGINT NOT NULL ,
`cccd` BIGINT NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`shool_name` VARCHAR(255)  NOT NULL ,
`image` VARCHAR(255)  NULL  DEFAULT "",
`address` VARCHAR(255)  NULL  DEFAULT "",
`building_group` TEXT NOT NULL ,
`building` TEXT NULL ,
`room` VARCHAR(255)  NULL  DEFAULT "",
`birthday` DATETIME NULL  DEFAULT NULL ,
`phone` VARCHAR(11)  NULL  DEFAULT "",
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `#__students_name` ON `#__students`(`name`);

CREATE INDEX `#__students_address` ON `#__students`(`address`);

CREATE INDEX `#__students_room` ON `#__students`(`room`);

CREATE INDEX `#__students_birthday` ON `#__students`(`birthday`);

CREATE INDEX `#__students_phone` ON `#__students`(`phone`);


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Student','com_students.student','{"special":{"dbtable":"#__students","key":"id","type":"StudentTable","prefix":"Joomla\\\\Component\\\\Students\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_students\/forms\/student.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_students.student')
) LIMIT 1;
