-- --------------------------------------------------------

CREATE TABLE `#__invites_tokens` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `inviter_id` bigint(11) unsigned NOT NULL,
  `service` varchar(20) DEFAULT NULL,
  `token` varchar(150) DEFAULT NULL,
  `used` tinyint(20) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `inviter_id` (`inviter_id`)
) ENGINE=InnoDB;

INSERT INTO #__migrator_versions (`version`,`component`) VALUES(1, 'invites') ON DUPLICATE KEY UPDATE `version` = 1;