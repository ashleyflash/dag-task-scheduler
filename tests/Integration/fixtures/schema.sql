DROP DATABASE IF EXISTS dag_scheduler_int;
CREATE DATABASE dag_scheduler_int;

USE dag_scheduler_int;

CREATE TABLE `process_dirty_value` (
  `value_type` char(1) NOT NULL DEFAULT '',
  `idvalue` int(10) unsigned NOT NULL,
  `log_filename_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `log_file_type` char(1) NOT NULL DEFAULT '',
  `log_filename_date_only` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`value_type`,`log_filename_date`,`idvalue`,`log_file_type`,`log_filename_date_only`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (TO_DAYS(`log_filename_date_only`))
(PARTITION pMAXVALUE VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

CREATE TABLE `process_lock` (
  `idprocess_dependency` smallint(3) unsigned NOT NULL,
  `lock_value` bigint(20) NOT NULL DEFAULT '0',
  `log_filename_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `process_status` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `attempts` smallint(5) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `started` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idprocess_dependency`,`log_filename_date`,`lock_value`),
  KEY `log_filename_date` (`log_filename_date`,`idprocess_dependency`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii
/*!50100 PARTITION BY RANGE (TO_DAYS(`log_filename_date`))
(PARTITION pMAXVALUE VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

CREATE TABLE `process_failure` (
  `idprocess_dependency` smallint(3) unsigned NOT NULL DEFAULT '0',
  `lock_value` bigint(20) NOT NULL DEFAULT '0',
  `log_filename_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attempt` smallint(5) unsigned NOT NULL DEFAULT '0',
  `process_failure` text DEFAULT NULL,
  `failure_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idprocess_dependency`,`log_filename_date`,`lock_value`,`attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (TO_DAYS(`log_filename_date`))
(PARTITION pMAXVALUE VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

-- Create syntax for TABLE 'process_dependency'
CREATE TABLE `process_dependency` (
  `idprocess_dependency` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idft_x_server_event_type` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `lock_name` char(30) NOT NULL DEFAULT '',
  `log_file_type` char(10) NOT NULL DEFAULT '',
  `process_type` char(30) NOT NULL DEFAULT '',
  `process_enabled` char(1) NOT NULL DEFAULT 'n',
  `dependency_period` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `process_order` tinyint(3) NOT NULL DEFAULT '0',
  `split_type` char(20) NOT NULL DEFAULT '',
  `job_name` varchar(255) NOT NULL DEFAULT '',
  `job_type` char(1) NOT NULL DEFAULT 'q',
  `queue` varchar(50) DEFAULT '',
  `duplicate_for_timezones` tinyint(1) NOT NULL DEFAULT '0',
  `force_push` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `queue_offset` tinyint(4) NOT NULL DEFAULT '0',
  `suspended` char(1) NOT NULL DEFAULT 'n',
  `suspended_in_turbo` char(1) NOT NULL DEFAULT 'n',
  `can_rerun` char(1) NOT NULL DEFAULT 'y',
  `start_after` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `idprocess_dependency_group` smallint(5) unsigned NOT NULL DEFAULT '0',
  `auto_complete_offset` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sequential` char(1) NOT NULL DEFAULT 'n',
  `sla` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`idprocess_dependency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'process_dependency_tree'
CREATE TABLE `process_dependency_tree` (
  `idprocess_dependency` smallint(5) unsigned NOT NULL,
  `idprocess_dependent` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idprocess_dependency`,`idprocess_dependent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'process_lock_expected'
CREATE TABLE `process_lock_expected` (
  `idprocess_dependency` smallint(5) unsigned NOT NULL,
  `expected_lock_value` bigint(20) NOT NULL DEFAULT '0',
  `log_filename_date` timestamp NOT NULL DEFAULT '1990-01-01 00:00:00',
  PRIMARY KEY (`idprocess_dependency`,`log_filename_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;