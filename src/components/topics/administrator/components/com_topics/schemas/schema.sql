CREATE TABLE `#__topics_topics` (
  `topics_topic_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` bigint(11) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`topics_topic_id`),
  KEY `node_id` (`node_id`)
) TYPE=InnoDB;

UPDATE #__migrator_versions SET `version` = 1 WHERE `component` = 'topics';