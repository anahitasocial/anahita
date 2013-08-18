-- --------------------------------------------------------

CREATE TABLE `#__anahita_edges` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
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
  KEY `end_date` (`end_date`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

CREATE TABLE `#__anahita_nodes` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `component` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `body` mediumtext,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `owner_type` varchar(255) DEFAULT NULL,
  `owner_id` bigint(11) unsigned DEFAULT NULL,
  `comment_status` tinyint(1) DEFAULT NULL,
  `comment_count` int(11) unsigned DEFAULT NULL,
  `last_comment_id` bigint(11) unsigned DEFAULT NULL,
  `last_comment_by` bigint(11) unsigned DEFAULT NULL,
  `last_comment_on` datetime DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `meta` text,
  `hits` int(11) unsigned DEFAULT NULL,
  `parent_id` bigint(11) unsigned DEFAULT NULL,
  `parent_type` varchar(255) DEFAULT NULL,
  `geo_latitude` float(10,6) DEFAULT NULL,
  `geo_longitude` float(10,6) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` bigint(11) unsigned DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` bigint(11) unsigned DEFAULT NULL,
  `actor_gender` varchar(50) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
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
  `tag_count` int(11) unsigned DEFAULT NULL,
  `tag_ids` text,
  `excerpt` text,
  `mimetype` varchar(100) DEFAULT NULL,
  `story_subject_id` bigint(11) unsigned DEFAULT NULL,
  `story_object_type` varchar(255) DEFAULT NULL,
  `story_object_id` bigint(11) unsigned DEFAULT NULL,
  `story_target_id` bigint(11) unsigned DEFAULT NULL,
  `story_comment_id` int(11) unsigned DEFAULT NULL,
  `person_userid` int(11) DEFAULT NULL,
  `person_username` varchar(255) DEFAULT NULL,
  `person_usertype` varchar(255) DEFAULT NULL,
  `person_useremail` varchar(255) DEFAULT NULL,
  `person_lastvisitdate` datetime DEFAULT NULL,
  `person_given_name` varchar(255) DEFAULT NULL,
  `person_family_name` varchar(255) DEFAULT NULL,
  `person_network_presence` varchar(255) DEFAULT NULL,
  `person_time_zone` int(11) DEFAULT NULL,
  `person_language` varchar(100) DEFAULT NULL,
  `access` text,
  `permissions` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `person_username` (`person_username`),
  UNIQUE KEY `person_userid` (`person_userid`),
  UNIQUE KEY `person_useremail` (`person_useremail`),
  KEY `last_comment_by` (`last_comment_by`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  KEY `person_lastvisitdate` (`person_lastvisitdate`),
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
) ENGINE=InnoDB;

-- --------------------------------------------------------

CREATE TABLE `#__components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `menuid` int(11) unsigned NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `admin_menu_link` varchar(255) NOT NULL DEFAULT '',
  `admin_menu_alt` varchar(255) NOT NULL DEFAULT '',
  `option` varchar(50) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `admin_menu_img` varchar(255) NOT NULL DEFAULT '',
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_option` (`parent`,`option`(32))
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__core_acl_aro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` varchar(240) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `jos_section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `jos_gacl_hidden_aro` (`hidden`),
  KEY `value` (`value`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__core_acl_aro_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `jos_gacl_parent_id_aro_groups` (`parent_id`),
  KEY `jos_gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__core_acl_aro_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(230) NOT NULL DEFAULT '0',
  `value` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__core_acl_aro_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(230) NOT NULL DEFAULT '',
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `jos_gacl_value_aro_sections` (`value`),
  KEY `jos_gacl_hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`),
  KEY `group_id` (`group_id`),
  KEY `aro_id` (`aro_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__groups` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menutype` varchar(75) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `link` text,
  `type` varchar(50) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `componentid` int(11) unsigned NOT NULL DEFAULT '0',
  `sublevel` int(11) DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL DEFAULT '0',
  `browserNav` tinyint(4) DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `utaccess` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL DEFAULT '0',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `home` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__menu_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menutype` varchar(75) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `menutype` (`menutype`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__migrator_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(255) NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `component` (`component`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `position` varchar(50) DEFAULT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `module` varchar(50) DEFAULT NULL,
  `numnews` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `showtitle` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  `control` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduleid`,`menuid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `iscore` tinyint(3) NOT NULL DEFAULT '0',
  `client_id` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__session` (
  `username` varchar(150) DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `session_id` varchar(200) NOT NULL DEFAULT '0',
  `guest` tinyint(4) DEFAULT '1',
  `userid` int(11) DEFAULT '0',
  `usertype` varchar(50) DEFAULT '',
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `client_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `data` longtext,
  PRIMARY KEY (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

CREATE TABLE `#__templates_menu` (
  `template` varchar(255) NOT NULL DEFAULT '',
  `menuid` int(11) NOT NULL DEFAULT '0',
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menuid`,`client_id`,`template`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `usertype` varchar(25) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `gid` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`,`block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM;

INSERT INTO #__migrator_versions (`version`,`component`) VALUES(3, 'anahita') ON DUPLICATE KEY UPDATE `version` = 3;