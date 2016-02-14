INSERT INTO `#__components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(1, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1),
(2, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1),
(3, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1),
(4, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1),
(5, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1),
(6, 'Components', 'option=com_components', 0, 0, 'option=com_components', 'Components', 'com_components', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(7, 'Dashboard', 'option=com_dashboard', 0, 0, '', 'Dashboard', 'com_dashboard', 0, '', 1, '', 1),
(8, 'People', 'option=com_people', 0, 0, 'option=com_people', 'People', 'com_people', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(9, 'Stories', '', 0, 0, '', 'Stories', 'com_stories', 0, '', 1, '', 1),
(10, 'Notifications', 'option=com_notifications', 0, 0, 'option=com_notifications', 'Notifications', 'com_notifications', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(11, 'Notes', 'option=com_notes', 0, 0, '', 'Notes', 'com_notes', 0, '', 1, '', 1),
(12, 'Pages', 'option=com_pages', 0, 0, 'option=com_pages', 'Pages', 'com_pages', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(13, 'Mailer', 'option=com_mailer', 0, 0, 'option=com_mailer', 'Mailer', 'com_mailer', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(14, 'Hashtags', 'option=com_hashtags', 0, 0, '', '', 'com_hashtags', 0, '', 1, '', 1),
(15, 'Locations', 'option=com_locations', 0, 0, 'option=com_locations', 'Locations', 'com_locations', 0, 'js/ThemeOffice/component.png', 1, '', 1);

INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(2, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n'),
(3, 'System - Anahita', 'anahita', 'system', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(4, 'Content Filter - Hyperlink', 'link', 'contentfilter', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(5, 'Content Filter - Video', 'video', 'contentfilter', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(6, 'Storage - Local', 'local', 'storage', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(7, 'Storage - Amazon S3', 's3', 'storage', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', ''),
(8, 'Content Filter - Hashtag', 'hashtag', 'contentfilter', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(9, 'Content Filter - Mention', 'mention', 'contentfilter', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(10, 'Content Filter - GithubGist', 'gist', 'contentfilter', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', ''),
(11, 'Content Filter - Medium', 'medium', 'contentfilter', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(12, 'Content Filter - Location', 'location', 'contentfilter', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', '');

INSERT INTO `#__nodes` (`id`, `type`, `component`, `name`, `access`) VALUES (1, 'ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment', 'com_notes', 'com:people.domain.entity.person', 1);

INSERT INTO `#__templates_menu` (`template`, `menuid`, `client_id`) VALUES
('shiraz', 0, 0),
('rt_missioncontrol_j15', 0, 1);

INSERT INTO #__migrator_versions (`version`,`component`) VALUES(3, 'anahita') ON DUPLICATE KEY UPDATE `version` = 3;
