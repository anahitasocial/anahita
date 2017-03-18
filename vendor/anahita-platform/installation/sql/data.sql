INSERT INTO `#__components` (`name`, `parent`, `option`, `ordering`, `iscore`, `meta`, `enabled`) VALUES
('Dashboard', 0, 'com_dashboard', 0, 1, '', 1),
('Search', 0, 'com_search', 0, 1, '', 1),
('People', 0, 'com_people', 0, 1, '', 1),
('Stories', 0, 'com_stories', 0, 1, '', 1),
('Notifications', 0, 'com_notifications', 0, 1, '', 1),
('Notes', 0, 'com_notes', 0, 1, '', 1),
('Pages', 0, 'com_pages', 0, 1, '', 1),
('Mailer', 0, 'com_mailer', 0, 1, '', 1),
('Hashtags', 0, 'com_hashtags', 0, 1, '', 1),
('Locations', 0, 'com_locations', 0, 1, '', 1);

-- --------------------------------------------------------

INSERT INTO `#__plugins` (`name`, `element`, `folder`, `ordering`, `enabled`, `iscore`, `meta`) VALUES
('Authentication', 'anahita', 'authentication', 1, 1, 1, ''),
('Anahita', 'anahita', 'user', 0, 1, 1, ''),
('Anahita', 'anahita', 'system', 1, 1, 1, ''),
('Hyperlink', 'link', 'contentfilter', 0, 1, 1, ''),
('Video', 'video', 'contentfilter', 0, 1, 1, ''),
('Local', 'local', 'storage', 0, 1, 1, '{"folder":"assets"}'),
('Amazon S3', 's3', 'storage', 0, 0, 1, ''),
('Hashtag', 'hashtag', 'contentfilter', 0, 1, 1, ''),
('Mention', 'mention', 'contentfilter', 0, 1, 1, ''),
('GithubGist', 'gist', 'contentfilter', 0, 0, 0, ''),
('Medium', 'medium', 'contentfilter', 0, 1, 1, ''),
('Location', 'location', 'contentfilter', 0, 1, 1, '');

-- --------------------------------------------------------

INSERT INTO `#__nodes` (`type`, `component`, `name`, `access`) VALUES ('ComComponentsDomainEntityAssignment,com:components.domain.entity.assignment', 'com_notes', 'com:people.domain.entity.person', 1);

-- --------------------------------------------------------

INSERT INTO `#__migrator_versions` (`version`,`component`) VALUES (21, 'anahita') ON DUPLICATE KEY UPDATE `version` = 21;
