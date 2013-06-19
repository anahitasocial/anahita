CREATE TABLE IF NOT EXISTS `#__topics_topics` (
  `topics_topic_id` bigint(11) unsigned NOT NULL auto_increment,
  `node_id` bigint(11) unsigned NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`topics_topic_id`),
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;