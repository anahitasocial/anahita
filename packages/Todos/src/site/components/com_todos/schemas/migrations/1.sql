CREATE TABLE IF NOT EXISTS `#__todos_milestones` (
  `todos_milestone_id` bigint(20) NOT NULL auto_increment,
  `node_id` bigint(11) NOT NULL,
  `todolists_count` int(11),
  PRIMARY KEY  (`todos_milestone_id`)
) ENGINE=InnoDB CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__todos_todolists` (
  `todos_todolist_id` bigint(20) NOT NULL auto_increment,
  `node_id` bigint(11) NOT NULL,
  `todos_count` int(11) NOT NULL,
  `open_todos_count` int(11) NOT NULL,
  PRIMARY KEY  (`todos_todolist_id`),
  UNIQUE KEY `node_id` (`node_id`)
) ENGINE=InnoDB CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

CREATE TABLE IF NOT EXISTS `#__todos_todos` (
  `todos_todo_id` bigint(20) NOT NULL auto_increment,
  `node_id` bigint(11) NOT NULL,
  `open_status_change_time` datetime,
  `open_status_change_by` bigint(11),
  PRIMARY KEY  (`todos_todo_id`),
  UNIQUE KEY `node_id` (`node_id`)
) ENGINE=InnoDB CHARACTER SET `utf8` COLLATE `utf8_general_ci`;
