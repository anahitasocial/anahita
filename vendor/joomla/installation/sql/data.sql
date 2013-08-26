INSERT INTO `#__components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`) VALUES
(14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1),
(21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1),
(23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1),
(25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1),
(28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1),
(29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1),
(30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1),
(31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1),
(32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1),
(33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1),
(35, 'Components', 'option=com_components', 0, 0, 'option=com_components', 'Components', 'com_components', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(37, 'Dashboard', 'option=com_dashboard', 0, 0, '', 'Dashboard', 'com_dashboard', 0, '', 1, '', 1),
(38, 'People', 'option=com_people', 0, 0, '', 'People', 'com_people', 0, '', 1, '', 1),
(39, 'Stories', '', 0, 0, '', 'Stories', 'com_stories', 0, '', 1, '', 1),
(40, 'Notifications', 'option=com_notifications', 0, 0, 'option=com_notifications', 'Notifications', 'com_notifications', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(41, 'Notes', 'option=com_notes', 0, 0, '', 'Notes', 'com_notes', 0, '', 1, '', 1),
(42, 'Html', 'option=com_html', 0, 0, 'option=com_html', 'Html', 'com_html', 0, 'js/ThemeOffice/component.png', 1, '', 1),
(43, 'Mailer', 'option=com_mailer', 0, 0, 'option=com_mailer', 'Mailer', 'com_mailer', 0, 'js/ThemeOffice/component.png', 1, '', 1);




INSERT INTO `#__core_acl_aro_groups` (`id`, `parent_id`, `name`, `lft`, `rgt`, `value`) VALUES
(17, 0, 'ROOT', 1, 22, 'ROOT'),
(28, 17, 'USERS', 2, 21, 'USERS'),
(29, 28, 'Public Frontend', 3, 12, 'Public Frontend'),
(18, 29, 'Registered', 4, 11, 'Registered'),
(19, 18, 'Author', 5, 10, 'Author'),
(20, 19, 'Editor', 6, 9, 'Editor'),
(21, 20, 'Publisher', 7, 8, 'Publisher'),
(30, 28, 'Public Backend', 13, 20, 'Public Backend'),
(23, 30, 'Manager', 14, 19, 'Manager'),
(24, 23, 'Administrator', 15, 18, 'Administrator'),
(25, 24, 'Super Administrator', 16, 17, 'Super Administrator');




INSERT INTO `#__core_acl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES
(10, 'users', 1, 'Users', 0);






INSERT INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(1, 'mainmenu', 'Home', 'home', 'index.php?option=com_html&view=content', 'component', 1, 0, 42, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, '', 0, 0, 1),
(4, 'mainmenu', 'People', 'people', 'index.php?option=com_people&view=people', 'component', 1, 0, 38, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(5, 'viewer', 'Your Social Graph', 'your-social-graph', 'index.php?option=com_people&view=person&get=graph', 'component', 1, 0, 38, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0);


INSERT INTO `#__menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Main Menu', 'The main menu for the site'),
(4, 'viewer', 'Viewer Menu', 'The menu that will show in the viewer module');


INSERT INTO `#__modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
(12, 'Admin Menu', '', 1, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 2, 1, '', 0, 1, ''),
(13, 'Admin SubMenu', '', 1, 'submenu', 0, '0000-00-00 00:00:00', 1, 'mod_submenu', 0, 2, 1, '', 0, 1, ''),
(27, 'Main Menu', '', 1, 'navigation', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 0, 0, 'menutype=mainmenu\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=1\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx= nav\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(28, 'Viewer', '', 1, 'viewer', 0, '0000-00-00 00:00:00', 1, 'mod_viewer', 0, 0, 0, 'menutype=viewer\n', 0, 0, '');


INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES
(1, 0),
(27, 0),
(28, 0);


INSERT INTO `#__plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) VALUES
(1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(5, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n'),
(36, 'System - Anahita', 'anahita', 'system', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(39, 'System - MissionControl Support', 'missioncontrol', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'patching=0\nblacklist=com_virtuemart,com_somethingelse\n\n'),
(41, 'Content Filter - Hyperlink', 'link', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(42, 'Content Filter - Syntax', 'syntax', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(43, 'Content Filter - Video', 'video', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(44, 'Content Filter - PTag', 'ptag', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', ''),
(45, 'Storage - Local', 'local', 'storage', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', ''),
(46, 'Storage - Amazon S3', 's3', 'storage', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');


INSERT INTO `#__templates_menu` (`template`, `menuid`, `client_id`) VALUES
('shiraz', 0, 0),
('rt_missioncontrol_j15', 0, 1);
