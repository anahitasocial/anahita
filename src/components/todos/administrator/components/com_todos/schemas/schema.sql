CREATE TABLE `#__todos_milestones` (
  `todos_milestone_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node_id` bigint(11) NOT NULL,
  `todolists_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`todos_milestone_id`)
) TYPE=InnoDB;

CREATE TABLE `#__todos_todolists` (
  `todos_todolist_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node_id` bigint(11) NOT NULL,
  `todos_count` int(11) NOT NULL,
  `open_todos_count` int(11) NOT NULL,
  PRIMARY KEY (`todos_todolist_id`),
  UNIQUE KEY `node_id` (`node_id`)
) TYPE=InnoDB;

CREATE TABLE `#__todos_todos` (
  `todos_todo_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `node_id` bigint(11) NOT NULL,
  `open_status_change_time` datetime DEFAULT NULL,
  `open_status_change_by` bigint(11) DEFAULT NULL,
  PRIMARY KEY (`todos_todo_id`),
  UNIQUE KEY `node_id` (`node_id`)
) TYPE=InnoDB;

UPDATE #__migrator_versions SET `version` = 0 WHERE `component` = 'todos';