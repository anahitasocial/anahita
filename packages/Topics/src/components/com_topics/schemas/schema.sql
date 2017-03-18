/* Set Migration Version */
INSERT INTO #__migrator_versions (`version`,`component`) VALUES(5, 'topics') ON DUPLICATE KEY UPDATE `version` = 5;
