-- --------------------------------------------------------

CREATE TABLE `#__connect_sessions` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(255) NOT NULL,
  `owner_id` bigint(11) NOT NULL,
  `owner_type` varchar(100) NOT NULL,
  `token_key` text,
  `token_secret` text,
  `api` varchar(100) NOT NULL,
  `update_status` tinyint(1) NOT NULL DEFAULT '0',
  `profile_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner_id_and_profile_id_and_service_id` (`owner_id`,`profile_id`,`api`)
) ENGINE=InnoDB;

INSERT INTO #__migrator_versions (`version`,`component`) VALUES(1, 'connect') ON DUPLICATE KEY UPDATE `version` = 1;