-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__anahita_edges`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__anahita_nodes`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__categories`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__components`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__content`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__content_frontpage`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_acl_aro`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_acl_aro_groups`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_acl_aro_map`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_acl_aro_sections`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_acl_groups_aro_map`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_log_items`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__core_log_searches`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__groups`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__menu`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__menu_types`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__migrator_versions`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__modules`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__modules_menu`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__plugins`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__sections`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__session`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__stats_agents`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__templates_menu`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `#__users`;

DELETE #__migrator_versions  WHERE `component` = 'anahita';