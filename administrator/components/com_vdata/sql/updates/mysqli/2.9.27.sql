-- ALTER TABLE `#__vd_config` ADD `multi_byte` tinyint(1) NOT NULL DEFAULT '0' AFTER `limit`;
ALTER TABLE `#__vd_profiles` ADD `source_enc` varchar(255) NULL AFTER `iotype`;
ALTER TABLE `#__vd_schedules` ADD `cron_restriction` INT NOT NULL DEFAULT '0' AFTER `iotype`;
