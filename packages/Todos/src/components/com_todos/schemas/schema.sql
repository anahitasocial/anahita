-- --------------------------------------------------------

CREATE TABLE `#__todos_todos` (
  `todos_todo_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `node_id` bigint(11) NOT NULL,
  `open_status_change_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `open_status_change_by` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`todos_todo_id`),
  UNIQUE KEY `node_id` (`node_id`)
) ENGINE=InnoDB;

INSERT INTO #__migrator_versions (`version`,`component`) VALUES(4, 'todos') ON DUPLICATE KEY UPDATE `version` = 4;
