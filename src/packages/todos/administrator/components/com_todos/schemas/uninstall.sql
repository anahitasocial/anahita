-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__todos_milestones`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__todos_todolists`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__todos_todos`;

DELETE #__migrator_versions  WHERE `component` = 'todos';