/* Set Migration Version */
INSERT INTO #__migrator_versions (`version`,`component`) VALUES(1, 'articles') ON DUPLICATE KEY UPDATE `version` = 1;
