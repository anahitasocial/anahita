INSERT INTO `#__categories` VALUES(1, 0, 'Anahita  Lingo', '', 'anahita-lingo', '', '1', 'left', '<p>Learn the Anahita Lingo</p>', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '');


INSERT INTO `#__components` VALUES (14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (15, 'Search', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1);
INSERT INTO `#__components` VALUES (16, 'Categories', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1);

INSERT INTO `#__components` VALUES (20, 'Articles', 'option=com_content', 0, 0, '', '', 'com_content', 0, '', 1, 'show_noauth=0\nshow_title=1\nlink_titles=0\nshow_intro=1\nshow_section=0\nlink_section=0\nshow_category=0\nlink_category=0\nshow_author=1\nshow_create_date=1\nshow_modify_date=1\nshow_item_navigation=0\nshow_readmore=1\nshow_vote=0\nshow_icons=1\nshow_pdf_icon=1\nshow_print_icon=1\nshow_email_icon=1\nshow_hits=1\nfeed_summary=0\n\n', 1);
INSERT INTO `#__components` VALUES (21, 'Configuration Manager', '', 0, 0, '', 'Configuration', 'com_config', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (22, 'Installation Manager', '', 0, 0, '', 'Installer', 'com_installer', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (23, 'Language Manager', '', 0, 0, '', 'Languages', 'com_languages', 0, '', 1, '', 1);

INSERT INTO `#__components` VALUES (25, 'Menu Editor', '', 0, 0, '', 'Menu Editor', 'com_menus', 0, '', 1, '', 1);

INSERT INTO `#__components` VALUES (28, 'Modules Manager', '', 0, 0, '', 'Modules', 'com_modules', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (29, 'Plugin Manager', '', 0, 0, '', 'Plugins', 'com_plugins', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (30, 'Template Manager', '', 0, 0, '', 'Templates', 'com_templates', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (31, 'User Manager', '', 0, 0, '', 'Users', 'com_users', 0, '', 1, 'allowUserRegistration=1\nnew_usertype=Registered\nuseractivation=1\nfrontend_userparams=1\n\n', 1);
INSERT INTO `#__components` VALUES (32, 'Cache Manager', '', 0, 0, '', 'Cache', 'com_cache', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (33, 'Control Panel', '', 0, 0, '', 'Control Panel', 'com_cpanel', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(36, 'Bazaar', 'option=com_bazaar', 0, 0, 'option=com_bazaar', 'Bazaar', 'com_bazaar', 0, 'js/ThemeOffice/component.png', 1, '', 1);
INSERT INTO `#__components` VALUES(35, 'Components', 'option=com_components', 0, 0, 'option=com_components', 'Components', 'com_components', 0, 'js/ThemeOffice/component.png', 1, '', 1);
INSERT INTO `#__components` VALUES(37, 'Dashboard', 'option=com_dashboard', 0, 0, '', 'Dashboard', 'com_dashboard', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(38, 'People', 'option=com_people', 0, 0, '', 'People', 'com_people', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(39, 'Stories', '', 0, 0, '', 'Stories', 'com_stories', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(40, 'Notifications', 'option=com_notifications', 0, 0, 'option=com_notifications', 'Notifications', 'com_notifications', 0, 'js/ThemeOffice/component.png', 1, '', 1);
INSERT INTO `#__components` VALUES(41, 'Notes', 'option=com_notes', 0, 0, '', 'Notes', 'com_notes', 0, '', 1, '', 1);


INSERT INTO `#__content` (`id`, `title`, `alias`, `title_alias`, `introtext`, `fulltext`, `state`, `sectionid`, `mask`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) VALUES
(1, 'Anahitapolis', 'anahitapolis', '', '<p>City of Anahita or the Home of Anahita project. The Anahita powered website where all the activities related to the Anahita project are happening. Those activities include managing and developing Anahita software, distributing, delivering support, and providing a learning environment for the <a href="http://anahitapolis.com/join">premium members</a> to learn how to install, configure, and develop for Anahita platform</p>', '', 1, 1, 0, 1, '0000-00-00 00:00:00', 62, '', '0000-00-00 00:00:00', 62, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 1, '', '', 0, 0, 'robots=\nauthor='),
(2, 'Anahita Framework', 'anahita-framework', '', '<p>A remarkable software development framework specialized for developing social networking applications following the nodes-graphs-stories architecture. It also makes use of a Domain Driven Design (DDD) pattern in all the models.</p>\r\n\r\n', '\r\n\r\n<p>Anahita framework has been developed using the Nooku Framework which is an MVC rapid application development framework itself. Nooku makes use of software development design patterns and DRY (Don''t Repeat Yourself) principals and that results into writing way less code that does way more. Such a code is also more secure and has less bugs. Visit <a href="http://www.nooku.org" target="_blank">Nooku.org</a> to learn more.</p>', 1, 1, 0, 1, '0000-00-00 00:00:00', 62, '', '0000-00-00 00:00:00', 62, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 2, '', '', 0, 0, 'robots=\nauthor='),
(3, 'NGS Architecture', 'ngs-architecture', '', '<p>Stands for the Nodes-Graphs-Stories architecture. In a social network everything and everybody are represented as nodes (people, groups, events, photos, topics, blog posts, etc.). The relationship amongst the nodes are maintained by the graphs. Stories propagate around in the network of nodes and graphs. Anahita  is developed following the correct Nodes-Graphs-Stories architecture of a social network. </p>', '', 1, 1, 0, 1, '0000-00-00 00:00:00', 62, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 3, '', '', 0, 0, 'robots=\nauthor=');


INSERT INTO `#__plugins` VALUES (1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (5, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n');
INSERT INTO `#__plugins` VALUES (12, 'Content - Pagebreak','pagebreak','content',0,10000,1,1,0,0,'0000-00-00 00:00:00','enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n');
INSERT INTO `#__plugins` VALUES (18, 'Editor - No Editor','none','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'mode=advanced\nskin=0\ncompressed=0\ncleanup_startup=0\ncleanup_save=2\nentity_encoding=raw\nlang_mode=0\nlang_code=en\ntext_direction=ltr\ncontent_css=1\ncontent_css_custom=\nrelative_urls=1\nnewlines=0\ninvalid_elements=applet\nextended_elements=\ntoolbar=top\ntoolbar_align=left\nhtml_height=550\nhtml_width=750\nelement_path=1\nfonts=1\npaste=1\nsearchreplace=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\ncolors=1\ntable=1\nsmilies=1\nmedia=1\nhr=1\ndirectionality=1\nfullscreen=1\nstyle=1\nlayer=1\nxhtmlxtras=1\nvisualchars=1\nnonbreaking=1\ntemplate=0\nadvimage=1\nadvlink=1\nautosave=1\ncontextmenu=1\ninlinepopups=1\nsafari=1\ncustom_plugin=\ncustom_button=\n\n');
INSERT INTO `#__plugins` VALUES (20, 'Editor - XStandard Lite 2.0', 'xstandard', 'editors', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (22, 'Editor Button - Pagebreak','pagebreak','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (23, 'Editor Button - Readmore','readmore','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (28, 'System - Debug', 'debug', 'system', 0, 2, 0, 0, 0, 0, '0000-00-00 00:00:00', 'queries=1\nmemory=1\nlangauge=1\n\n');
INSERT INTO `#__plugins` VALUES (32, 'System - Remember Me', 'remember', 'system', 0, 6, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (36, 'System - Anahita', 'anahita', 'system', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (39, 'System - MissionControl Support', 'missioncontrol', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'patching=0\nblacklist=com_virtuemart,com_somethingelse\n\n');
INSERT INTO `#__plugins` VALUES (40, 'Content - SEF', 'sef', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (41, 'Content Filter - Hyperlink', 'link', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (42, 'Content Filter - Syntax', 'syntax', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (43, 'Content Filter - Video', 'video', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (44, 'Content Filter - PTag', 'ptag', 'contentfilter', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (45, 'Storage - Local', 'local',  'storage', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (46, 'Storage - Amazon S3', 's3', 'storage', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (47, 'Installer - Core', 'core', 'installer', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');


INSERT INTO `#__menu` (`id`, `menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
(1, 'mainmenu', 'Home', 'home', 'index.php?option=com_content&view=frontpage', 'component', 1, 0, 20, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 'num_leading_articles=1\nnum_intro_articles=4\nnum_columns=2\nnum_links=4\norderby_pri=\norderby_sec=front\nshow_pagination=2\nshow_pagination_results=1\nshow_feed_link=1\nshow_noauth=\nshow_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_item_navigation=\nshow_readmore=\nshow_vote=\nshow_icons=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nshow_hits=\nfeed_summary=\npage_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 1),
(2, 'mainmenu', 'Dashboard', 'dashboard', 'index.php?option=com_dashboard', 'component', 1, 0, 37, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(4, 'mainmenu', 'People', 'people', 'index.php?option=com_people&view=people', 'component', 1, 0, 38, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(5, 'viewer', 'Your Social Graph', 'your-social-graph', 'index.php?option=com_people&view=person&get=graph', 'component', 1, 0, 38, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0),
(6, 'footer-about', 'Anahita Social Networking Platform', 'anahita-social-networking-platform', 'http://www.anahitapolis.com/download/anahita', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(8, 'footer-about', 'Anahita Core Architects', 'anahita-core-architects', 'http://www.anahitapolis.com/about/anahita-core-architects', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(9, 'footer-about', 'Anahita First Tribe', 'anahita-first-tribe', 'http://www.anahitapolis.com/about/anahita-first-tribe', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(10, 'footer-about', 'Join', 'join', 'http://www.anahitapolis.com/join', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(11, 'socialweb-footer', 'Anahitapolis Blog', 'anahitapolis-blog', 'http://blog.anahitapolis.com', 'url', 1, 0, 0, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(12, 'socialweb-footer', 'Twitter @anahitapolis', 'twitter-anahitapolis', 'http://www.twitter.com/anahitapolis', 'url', 1, 0, 0, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(13, 'socialweb-footer', 'Facebook Page', 'facebook-page', 'http://www.facebook.com/anahitasocial', 'url', 1, 0, 0, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(14, 'socialweb-footer', 'Anahitapolis (Home Of Anahita Project)', 'anahitapolis-home-of-anahita-project', 'http://www.anahitapolis.com', 'url', 1, 0, 0, 0, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0),
(15, 'socialweb-footer', 'Anahita Podcast', 'anahita-podcast', 'http://itunes.apple.com/ca/podcast/anahitapolis-blog/id485431193', 'url', 1, 0, 0, 0, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\n\n', 0, 0, 0);


INSERT INTO `#__menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Main Menu', 'The main menu for the site'),
(2, 'footer-about', 'About', ''),
(3, 'socialweb-footer', 'Follow Our Updates', ''),
(4, 'viewer','Viewer Menu','The menu that will show in the viewer module')
;

INSERT INTO `#__modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
(2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, ''),
(12, 'Admin Menu', '', 1, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 2, 1, '', 0, 1, ''),
(13, 'Admin SubMenu', '', 1, 'submenu', 0, '0000-00-00 00:00:00', 1, 'mod_submenu', 0, 2, 1, '', 0, 1, ''),
(21, 'Login', '', 1, 'maintop-c', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, 'cache=0\nmoduleclass_sfx=\npretext=\nposttext=\nlogin=2\nlogout=1\ngreeting=1\nname=0\nusesecure=0\n\n', 0, 0, ''),
(31, 'Free Tribe Membership', '<p>Do you have any questions that need to be answered before signing up as a <a href="http://www.anahitapolis.com/join/premium">premium tribe</a> member?</p>\r\n<p><a class="btn" href="http://www.anahitapolis.com/join/free">Join Now!</a></p>', 2, 'maintop-b', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, ''),
(32, 'Homepage Hero Unit', '<div class="hero-unit">\r\n<h1>Anahita®</h1>\r\n<p>a developer friendly and open source social networking platform and framework that helps you build the foundations of your apps and services in less time using a correct nodes-graphs-stories architecture.</p>\r\n<p><a class="btn btn-large btn-primary" href="http://www.anahitapolis.com">Learn More</a></p>\r\n</div>', 0, 'showcase-a', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=\n\n', 0, 0, ''),
(24, 'Follow Our Updates', '', 2, 'maintop-a', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 0, 1, 'menutype=socialweb-footer\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(25, 'Developed By', '<p>Anahita® is developed by <a href="http://www.peerglobe.com" target="_blank">Peerglobe Technology</a> and <a href="http://www.rmdstudio.com" target="_blank">rmd Studio</a> who are also the co-founders of the <a href="http://www.purplerat.com" target="_blank">Purplerat Tribe of Companies</a> in Vancouver, BC, Canada.</p>', 0, 'footer-a', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=\n\n', 0, 0, ''),
(27, 'Main Menu', '', 1, 'navigation', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 0, 0, 'menutype=mainmenu\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=1\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx= nav\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(28, 'Viewer', '', 1, 'viewer', 0, '0000-00-00 00:00:00', 1, 'mod_viewer', 0, 0, 0, 'menutype=viewer\n', 0, 0, ''),
(30, 'Premium Tribe Membership', '<p><strong>Get all the Anahita resources that you need!</strong></p>\r\n<p>Our 12 months Platinum, Gold, and Executive premium plans provide you all the resources that you need to execute and launch your social networking project or business idea.</p>\r\n<p><a class="btn btn-primary" href="http://www.anahitapolis.com/join/premium">Join Now!</a></p>', 0, 'maintop-b', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, '');


INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES
(1, 0),
(21, 1),
(24, 1),
(25, 1),
(27, 0),
(28, 0),
(30, 1),
(31, 1),
(32, 1);

INSERT INTO `#__sections` (`id`, `title`, `name`, `alias`, `image`, `scope`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `ordering`, `access`, `count`, `params`) VALUES
(1, 'About', '', 'about', '', 'content', 'left', '', 1, 0, '0000-00-00 00:00:00', 2, 0, 1, '');


INSERT INTO `#__templates_menu` (`template`, `menuid`, `client_id`) VALUES
('shiraz', 0, 0),
('rt_missioncontrol_j15', 0, 1);

INSERT INTO `#__core_acl_aro_groups` VALUES (17,0,'ROOT',1,22,'ROOT');
INSERT INTO `#__core_acl_aro_groups` VALUES (28,17,'USERS',2,21,'USERS');
INSERT INTO `#__core_acl_aro_groups` VALUES (29,28,'Public Frontend',3,12,'Public Frontend');
INSERT INTO `#__core_acl_aro_groups` VALUES (18,29,'Registered',4,11,'Registered');
INSERT INTO `#__core_acl_aro_groups` VALUES (19,18,'Author',5,10,'Author');
INSERT INTO `#__core_acl_aro_groups` VALUES (20,19,'Editor',6,9,'Editor');
INSERT INTO `#__core_acl_aro_groups` VALUES (21,20,'Publisher',7,8,'Publisher');
INSERT INTO `#__core_acl_aro_groups` VALUES (30,28,'Public Backend',13,20,'Public Backend');
INSERT INTO `#__core_acl_aro_groups` VALUES (23,30,'Manager',14,19,'Manager');
INSERT INTO `#__core_acl_aro_groups` VALUES (24,23,'Administrator',15,18,'Administrator');
INSERT INTO `#__core_acl_aro_groups` VALUES (25,24,'Super Administrator',16,17,'Super Administrator');

INSERT INTO `#__core_acl_aro_sections` VALUES (10,'users',1,'Users',0);
