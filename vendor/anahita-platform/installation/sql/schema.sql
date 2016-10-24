-- --------------------------------------------------------

CREATE TABLE `#__edges` (
  `id` SERIAL,
  `type` varchar(255) NOT NULL,
  `node_a_id` bigint(11) unsigned NOT NULL,
  `node_a_type` varchar(255) NOT NULL,
  `node_b_id` bigint(11) unsigned NOT NULL,
  `node_b_type` varchar(255) NOT NULL,
  `meta` text,
  `created_on` datetime DEFAULT NULL,
  `created_by` bigint(11) unsigned DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` bigint(11) unsigned DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `node_a_id` (`node_a_id`),
  KEY `node_b_id` (`node_b_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  CONSTRAINT uc_edge UNIQUE(`type`,`node_a_id`,`node_a_type`,`node_b_id`,`node_b_type`)
) ENGINE=InnoDB CHARACTER SET=utf8;

-- --------------------------------------------------------

CREATE TABLE `#__nodes` (
  `id` SERIAL,
  `type` varchar(255) NOT NULL,
  `component` varchar(100) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4,
  `alias` varchar(255) CHARACTER SET utf8mb4,
  `body` mediumtext CHARACTER SET utf8mb4,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `owner_type` varchar(255) DEFAULT NULL,
  `owner_id` bigint(11) unsigned DEFAULT NULL,
  `comment_status` tinyint(1) DEFAULT NULL,
  `comment_count` int(11) unsigned DEFAULT NULL,
  `last_comment_id` bigint(11) unsigned DEFAULT NULL,
  `last_comment_by` bigint(11) unsigned DEFAULT NULL,
  `last_comment_on` datetime DEFAULT NULL,
  `ordering` int(11) DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `meta` text DEFAULT NULL,
  `hits` int(11) unsigned DEFAULT NULL,
  `parent_id` bigint(11) unsigned DEFAULT NULL,
  `parent_type` varchar(255) DEFAULT NULL,
  `geo_latitude` float(10,6) DEFAULT NULL,
  `geo_longitude` float(10,6) DEFAULT NULL,
  `geo_address` VARCHAR(255) DEFAULT NULL,
  `geo_city` VARCHAR(50) DEFAULT NULL,
  `geo_state_province` VARCHAR(50) DEFAULT NULL,
  `geo_country` VARCHAR(30) DEFAULT NULL,
  `geo_postalcode` VARCHAR(15) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` bigint(11) unsigned DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` bigint(11) unsigned DEFAULT NULL,
  `actor_gender` varchar(50) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `cover_filename` varchar(255) DEFAULT NULL,
  `cover_filesize` int(11) DEFAULT NULL,
  `cover_mimetype` varchar(100) DEFAULT NULL,
  `status` text,
  `status_update_time` datetime DEFAULT NULL,
  `follower_count` int(11) unsigned DEFAULT NULL,
  `leader_count` int(11) unsigned DEFAULT NULL,
  `mutual_count` int(11) unsigned DEFAULT NULL,
  `leader_ids` mediumtext,
  `follower_ids` mediumtext,
  `mutual_ids` mediumtext,
  `blocker_ids` text,
  `allow_follow_request` tinyint(1) unsigned DEFAULT NULL,
  `follow_requester_ids` mediumtext,
  `subscriber_count` int(11) unsigned DEFAULT NULL,
  `subscriber_ids` mediumtext,
  `administrating_ids` text,
  `notification_ids` text,
  `new_notification_ids` text,
  `vote_up_count` int(11) unsigned DEFAULT NULL,
  `vote_down_count` int(11) unsigned DEFAULT NULL,
  `voter_up_ids` mediumtext,
  `voter_down_ids` mediumtext,
  `shared_owner_count` int(11) unsigned DEFAULT NULL,
  `shared_owner_ids` mediumtext,
  `administrator_ids` text,
  `blocked_ids` text,
  `excerpt` text CHARACTER SET utf8mb4,
  `mimetype` varchar(100) DEFAULT NULL,
  `story_subject_id` bigint(11) unsigned DEFAULT NULL,
  `story_object_type` varchar(255) DEFAULT NULL,
  `story_object_id` bigint(11) unsigned DEFAULT NULL,
  `story_target_id` bigint(11) unsigned DEFAULT NULL,
  `story_comment_id` int(11) unsigned DEFAULT NULL,
  `time_zone` int(11) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `access` text,
  `permissions` text,
  PRIMARY KEY (`id`),
  KEY `last_comment_by` (`last_comment_by`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `type` (`type`),
  KEY `component` (`component`),
  KEY `owner_id` (`owner_id`),
  KEY `parent_id` (`parent_id`),
  KEY `story_target_id` (`story_target_id`),
  KEY `story_object_id` (`story_object_id`),
  KEY `story_subject_id` (`story_subject_id`),
  KEY `story_comment_id` (`story_comment_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `type_enabled` (`type`,`enabled`),
  KEY `type_modifed_on` (`type`,`modified_on`),
  KEY `type_created_on` (`type`,`created_on`),
  KEY `type_status_update_time` (`type`,`status_update_time`),
  KEY `type_default` (`type`,`is_default`)
) ENGINE=InnoDB CHARACTER SET=utf8;

-- --------------------------------------------------------

CREATE TABLE `#__components` (
  `id` SERIAL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `option` varchar(50) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `meta` text DEFAULT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_option` (`parent`,`option`(32))
) ENGINE=InnoDB CHARACTER SET=utf8;

-- --------------------------------------------------------

CREATE TABLE `#__migrator_versions` (
  `id` SERIAL,
  `component` varchar(255) NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `component` (`component`)
) ENGINE=InnoDB CHARACTER SET=utf8;

-- --------------------------------------------------------

CREATE TABLE `#__plugins` (
  `id` SERIAL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `enabled` tinyint(3) NOT NULL DEFAULT 0,
  `iscore` tinyint(3) NOT NULL DEFAULT 0,
  `meta` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_folder` (`enabled`,`folder`)
) ENGINE=InnoDB CHARACTER SET=utf8;

-- --------------------------------------------------------

CREATE TABLE `#__sessions` (
    `id` SERIAL,
    `session_id` char(64) NOT NULL,
    `node_id` bigint(11) NOT NULL DEFAULT 0,
    `username` varchar(255) DEFAULT NULL,
    `usertype` varchar(255),
    `time` INT(11) DEFAULT 0,
    `guest` tinyint(2) DEFAULT '1',
    `meta` longtext,
    PRIMARY KEY (`id`),
    KEY `whosonline` (`guest`,`usertype`,`username`),
    UNIQUE KEY `session_id` (`session_id`),
    KEY `node_id` (`node_id`),
    KEY `username` (`username`)
) ENGINE=InnoDB CHARACTER SET=utf8;

-- --------------------------------------------------------

CREATE TABLE `#__people_people` (
    `people_person_id` SERIAL,
    `node_id` BIGINT UNSIGNED NOT NULL,
    `email` varchar(255) DEFAULT NULL,
    `username` varchar(255) DEFAULT NULL,
    `password` varchar(255) DEFAULT NULL,
    `usertype` varchar(50) DEFAULT NULL,
    `gender` varchar(50) DEFAULT NULL,
    `given_name` varchar(255) DEFAULT NULL,
    `family_name` varchar(255) DEFAULT NULL,
    `network_presence` tinyint(3) NOT NULL DEFAULT 0,
    `last_visit_date` datetime DEFAULT NULL,
    `time_zone` int(11) DEFAULT NULL,
    `language` varchar(100) DEFAULT NULL,
    `activation_code` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`people_person_id`),
    KEY `usertype` (`usertype`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `node_id` (`node_id`),
    KEY `last_visit_date` (`last_visit_date`)
) ENGINE=InnoDB CHARACTER SET=utf8;
