# $Id: joomla.sql 18130 2010-07-14 11:21:35Z louis $

# --------------------------------------------------------

#
# Table structure for table `#__categories`
#

CREATE TABLE `#__categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

INSERT INTO `#__categories` VALUES(1, 0, 'Anahita  Lingo', '', 'anahita-lingo', '', '1', 'left', '<p>Learn the Anahita Lingo</p>', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '');

# --------------------------------------------------------

#
# Table structure for table `#__components`
#

CREATE TABLE `#__components` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `menuid` int(11) unsigned NOT NULL default '0',
  `parent` int(11) unsigned NOT NULL default '0',
  `admin_menu_link` varchar(255) NOT NULL default '',
  `admin_menu_alt` varchar(255) NOT NULL default '',
  `option` varchar(50) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  `admin_menu_img` varchar(255) NOT NULL default '',
  `iscore` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  `enabled` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `parent_option` (`parent`, `option`(32))
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

#
# Dumping data for table `#__components`
#


INSERT INTO `#__components` VALUES (14, 'User', 'option=com_user', 0, 0, '', '', 'com_user', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES (15, 'Search', 'option=com_search', 0, 0, 'option=com_search', 'Search Statistics', 'com_search', 0, 'js/ThemeOffice/component.png', 1, 'enabled=0\n\n', 1);
INSERT INTO `#__components` VALUES (16, 'Categories', '', 0, 1, 'option=com_categories&section=com_banner', 'Categories', '', 3, '', 1, '', 1);

#Media Manager
#INSERT INTO `#__components` VALUES (19, 'Media Manager', '', 0, 0, 'option=com_media', 'Media Manager', 'com_media', 0, '', 1, 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS\nupload_maxsize=10000000\nfile_path=images\nimage_path=images/stories\nrestrict_uploads=1\ncheck_mime=1\nimage_extensions=bmp,gif,jpg,png\nignore_extensions=\nupload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip\nupload_mime_illegal=text/html', 1);

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
INSERT INTO `#__components` VALUES(35, 'Apps', 'option=com_apps', 0, 0, 'option=com_apps', 'Apps', 'com_apps', 0, 'js/ThemeOffice/component.png', 1, '', 1);
INSERT INTO `#__components` VALUES(37, 'Dashboard', 'option=com_dashboard', 0, 0, '', 'Dashboard', 'com_dashboard', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(38, 'People', 'option=com_people', 0, 0, '', 'People', 'com_people', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(39, 'Stories', '', 0, 0, '', 'Stories', 'com_stories', 0, '', 1, '', 1);
INSERT INTO `#__components` VALUES(40, 'Notifications', 'option=com_notifications', 0, 0, 'option=com_notifications', 'Notifications', 'com_notifications', 0, 'js/ThemeOffice/component.png', 1, '', 1);
 
# --------------------------------------------------------

#
# Table structure for table `#__content`
#

CREATE TABLE `#__content` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `title_alias` varchar(255) NOT NULL default '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL default '0',
  `sectionid` int(11) unsigned NOT NULL default '0',
  `mask` int(11) unsigned NOT NULL default '0',
  `catid` int(11) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL default '0',
  `created_by_alias` varchar(255) NOT NULL default '',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` text NOT NULL,
  `version` int(11) unsigned NOT NULL default '1',
  `parentid` int(11) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0',
  `metadata` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `idx_section` (`sectionid`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

INSERT INTO `#__content` (`id`, `title`, `alias`, `title_alias`, `introtext`, `fulltext`, `state`, `sectionid`, `mask`, `catid`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `images`, `urls`, `attribs`, `version`, `parentid`, `ordering`, `metakey`, `metadesc`, `access`, `hits`, `metadata`) VALUES
(1, 'Anahitapolis', 'anahitapolis', '', '<p>City of Anahita or the Home of Anahita project. The Anahita powered website where all the activities related to the Anahita project are happening. Those activities include managing and developing Anahita software, distributing, delivering support, and providing a learning environment for the <a href="http://anahitapolis.com/join">premium members</a> to learn how to install, configure, and develop for Anahita platform</p>', '', 1, 1, 0, 1, '0000-00-00 00:00:00', 62, '', '0000-00-00 00:00:00', 62, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 1, '', '', 0, 0, 'robots=\nauthor='),
(2, 'Anahita Framework', 'anahita-framework', '', '<p>A remarkable software development framework specialized for developing social networking applications following the nodes-graphs-stories architecture. It also makes use of a Domain Driven Design (DDD) pattern in all the models.</p>\r\n\r\n', '\r\n\r\n<p>Anahita framework has been developed using the Nooku Framework which is an MVC rapid application development framework itself. Nooku makes use of software development design patterns and DRY (Don''t Repeat Yourself) principals and that results into writing way less code that does way more. Such a code is also more secure and has less bugs. Visit <a href="http://www.nooku.org" target="_blank">Nooku.org</a> to learn more.</p>', 1, 1, 0, 1, '0000-00-00 00:00:00', 62, '', '0000-00-00 00:00:00', 62, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 2, '', '', 0, 0, 'robots=\nauthor='),
(3, 'NGS Architecture', 'ngs-architecture', '', '<p>Stands for the Nodes-Graphs-Stories architecture. In a social network everything and everybody are represented as nodes (people, groups, events, photos, topics, blog posts, etc.). The relationship amongst the nodes are maintained by the graphs. Stories propagate around in the network of nodes and graphs. Anahita  is developed following the correct Nodes-Graphs-Stories architecture of a social network. </p>', '', 1, 1, 0, 1, '0000-00-00 00:00:00', 62, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 'show_title=\nlink_titles=\nshow_intro=\nshow_section=\nlink_section=\nshow_category=\nlink_category=\nshow_vote=\nshow_author=\nshow_create_date=\nshow_modify_date=\nshow_pdf_icon=\nshow_print_icon=\nshow_email_icon=\nlanguage=\nkeyref=\nreadmore=', 1, 0, 3, '', '', 0, 0, 'robots=\nauthor=');

# --------------------------------------------------------

#
# Table structure for table `#__content_frontpage`
#

CREATE TABLE `#__content_frontpage` (
  `content_id` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__content_rating`
#

CREATE TABLE `#__content_rating` (
  `content_id` int(11) NOT NULL default '0',
  `rating_sum` int(11) unsigned NOT NULL default '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `lastip` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`content_id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

# Table structure for table `#__core_log_items`

CREATE TABLE `#__core_log_items` (
  `time_stamp` date NOT NULL default '0000-00-00',
  `item_table` varchar(50) NOT NULL default '',
  `item_id` int(11) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

# Table structure for table `#__core_log_searches`

CREATE TABLE `#__core_log_searches` (
  `search_term` varchar(128) NOT NULL default '',
  `hits` int(11) unsigned NOT NULL default '0'
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

#
# Table structure for table `#__groups`
#

# --------------------------------------------------------

CREATE TABLE `#__groups` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

#
# Dumping data for table `#__groups`
#

INSERT INTO `#__groups` VALUES (0, 'Public');
INSERT INTO `#__groups` VALUES (1, 'Registered');
INSERT INTO `#__groups` VALUES (2, 'Special');

# --------------------------------------------------------

#
# Table structure for table `#__plugins`
#

CREATE TABLE `#__plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `iscore` tinyint(3) NOT NULL default '0',
  `client_id` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_folder` (`published`,`client_id`,`access`,`folder`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

INSERT INTO `#__plugins` VALUES (1, 'Authentication - Joomla', 'joomla', 'authentication', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (5, 'User - Joomla!', 'joomla', 'user', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', 'autoregister=1\n\n');
INSERT INTO `#__plugins` VALUES (6, 'Search - Content','content','search',0,1,1,1,0,0,'0000-00-00 00:00:00','search_limit=50\nsearch_content=1\nsearch_uncategorised=1\nsearch_archived=1\n\n');
INSERT INTO `#__plugins` VALUES (8, 'Search - Categories', 'categories', 'search', 0, 4, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (9, 'Search - Sections', 'sections', 'search', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'search_limit=50\n\n');
INSERT INTO `#__plugins` VALUES (12, 'Content - Pagebreak','pagebreak','content',0,10000,1,1,0,0,'0000-00-00 00:00:00','enabled=1\ntitle=1\nmultipage_toc=1\nshowall=1\n\n');
INSERT INTO `#__plugins` VALUES (14, 'Content - Email Cloaking', 'emailcloak', 'content', 0, 5, 1, 0, 0, 0, '0000-00-00 00:00:00', 'mode=1\n\n');
INSERT INTO `#__plugins` VALUES (16, 'Content - Load Module', 'loadmodule', 'content', 0, 6, 1, 0, 0, 0, '0000-00-00 00:00:00', 'enabled=1\nstyle=0\n\n');
INSERT INTO `#__plugins` VALUES (17, 'Content - Page Navigation','pagenavigation','content',0,2,1,1,0,0,'0000-00-00 00:00:00','position=1\n\n');
INSERT INTO `#__plugins` VALUES (18, 'Editor - No Editor','none','editors',0,0,1,1,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (19, 'Editor - TinyMCE', 'tinymce', 'editors', 0, 0, 1, 1, 0, 0, '0000-00-00 00:00:00', 'mode=advanced\nskin=0\ncompressed=0\ncleanup_startup=0\ncleanup_save=2\nentity_encoding=raw\nlang_mode=0\nlang_code=en\ntext_direction=ltr\ncontent_css=1\ncontent_css_custom=\nrelative_urls=1\nnewlines=0\ninvalid_elements=applet\nextended_elements=\ntoolbar=top\ntoolbar_align=left\nhtml_height=550\nhtml_width=750\nelement_path=1\nfonts=1\npaste=1\nsearchreplace=1\ninsertdate=1\nformat_date=%Y-%m-%d\ninserttime=1\nformat_time=%H:%M:%S\ncolors=1\ntable=1\nsmilies=1\nmedia=1\nhr=1\ndirectionality=1\nfullscreen=1\nstyle=1\nlayer=1\nxhtmlxtras=1\nvisualchars=1\nnonbreaking=1\ntemplate=0\nadvimage=1\nadvlink=1\nautosave=1\ncontextmenu=1\ninlinepopups=1\nsafari=1\ncustom_plugin=\ncustom_button=\n\n');
INSERT INTO `#__plugins` VALUES (20, 'Editor - XStandard Lite 2.0', 'xstandard', 'editors', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `#__plugins` VALUES (22, 'Editor Button - Pagebreak','pagebreak','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (23, 'Editor Button - Readmore','readmore','editors-xtd',0,0,1,0,0,0,'0000-00-00 00:00:00','');
INSERT INTO `#__plugins` VALUES (28, 'System - Debug', 'debug', 'system', 0, 2, 0, 0, 0, 0, '0000-00-00 00:00:00', 'queries=1\nmemory=1\nlangauge=1\n\n');
INSERT INTO `#__plugins` VALUES (30, 'System - Cache', 'cache', 'system', 0, 4, 0, 1, 0, 0, '0000-00-00 00:00:00', 'browsercache=0\ncachetime=15\n\n');
INSERT INTO `#__plugins` VALUES (31, 'System - Log', 'log', 'system', 0, 5, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
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
       
# --------------------------------------------------------

#
# Table structure for table `#__menu`
#

CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL auto_increment,
  `menutype` varchar(75) default NULL,
  `name` varchar(255) default NULL,
  `alias` varchar(255) NOT NULL default '',
  `link` text,
  `type` varchar(50) NOT NULL default '',
  `published` tinyint(1) NOT NULL default 0,
  `parent` int(11) unsigned NOT NULL default 0,
  `componentid` int(11) unsigned NOT NULL default 0,
  `sublevel` int(11) default 0,
  `ordering` int(11) default 0,
  `checked_out` int(11) unsigned NOT NULL default 0,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pollid` int(11) NOT NULL default 0,
  `browserNav` tinyint(4) default 0,
  `access` tinyint(3) unsigned NOT NULL default 0,
  `utaccess` tinyint(3) unsigned NOT NULL default 0,
  `params` text NOT NULL,
  `lft` int(11) unsigned NOT NULL default 0,
  `rgt` int(11) unsigned NOT NULL default 0,
  `home` INTEGER(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`),
  KEY `componentid` (`componentid`,`menutype`,`published`,`access`),
  KEY `menutype` (`menutype`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

--
-- Dumping data for table `#__menu`
--

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

# --------------------------------------------------------

#
# Table structure for table `#__menu_types`
#

CREATE TABLE `#__menu_types` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `menutype` VARCHAR(75) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`),
  UNIQUE `menutype`(`menutype`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

--
-- Dumping data for table `#__menu_types`
--

INSERT INTO `#__menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(1, 'mainmenu', 'Main Menu', 'The main menu for the site'),
(2, 'footer-about', 'About', ''),
(3, 'socialweb-footer', 'Follow Our Updates', ''),
(4, 'viewer','Viewer Menu','The menu that will show in the viewer module')
;

# --------------------------------------------------------

#
# Table structure for table `#__modules`
#

CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `position` varchar(50) default NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `numnews` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `showtitle` tinyint(3) unsigned NOT NULL default '1',
  `params` text NOT NULL,
  `iscore` tinyint(4) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  `control` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`,`access`),
  KEY `newsfeeds` (`module`,`published`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

--
-- Dumping data for table `#__modules`
--

INSERT INTO `#__modules` (`id`, `title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
(2, 'Login', '', 1, 'login', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, '', 1, 1, ''),
(8, 'Toolbar', '', 1, 'toolbar', 0, '0000-00-00 00:00:00', 1, 'mod_toolbar', 0, 2, 1, '', 1, 1, ''),
(11, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1, ''),
(12, 'Admin Menu', '', 1, 'menu', 0, '0000-00-00 00:00:00', 1, 'mod_menu', 0, 2, 1, '', 0, 1, ''),
(13, 'Admin SubMenu', '', 1, 'submenu', 0, '0000-00-00 00:00:00', 1, 'mod_submenu', 0, 2, 1, '', 0, 1, ''),
(15, 'Title', '', 1, 'title', 0, '0000-00-00 00:00:00', 1, 'mod_title', 0, 2, 1, '', 0, 1, ''),
(16, 'Quick Links', '', 0, 'dashboard', 0, '0000-00-00 00:00:00', 1, 'mod_rokquicklinks', 0, 0, 1, 'title-1=Bazaar\nlink-1=index.php?option=com_bazaar\nicon-1=anahita.png\ntitle-2=Social Apps\nlink-2=index.php?option=com_apps\nicon-2=application_view_icons.png\ntitle-3=Plugins\nlink-3=index.php?option=com_plugins\nicon-3=brick.png\ntitle-4=Modules\nlink-4=index.php?option=com_modules\nicon-4=brick.png\ntitle-5=Templates\nlink-5=index.php?option=com_templates\nicon-5=color_management.png\ntitle-6=Extend\nlink-6=index.php?option=com_installer\nicon-6=package.png\ntitle-7=Configuration\nlink-7=index.php?option=com_config\nicon-7=cog.png\nquickfields=[{"icon":"anahita.png","link":"index.php?option=com_bazaar","title":"Bazaar"},{"icon":"application_view_icons.png","link":"index.php?option=com_apps","title":"Social Apps"},{"icon":"brick.png","link":"index.php?option=com_plugins","title":"Plugins"},{"icon":"brick.png","link":"index.php?option=com_modules","title":"Modules"},{"icon":"color_management.png","link":"index.php?option=com_templates","title":"Templates"},{"icon":"package.png","link":"index.php?option=com_installer","title":"Extend"},{"icon":"cog.png","link":"index.php?option=com_config","title":"Configuration"}]\n\n', 0, 1, ''),
(21, 'Login', '', 1, 'maintop-c', 0, '0000-00-00 00:00:00', 1, 'mod_login', 0, 0, 1, 'cache=0\nmoduleclass_sfx=\npretext=\nposttext=\nlogin=2\nlogout=1\ngreeting=1\nname=0\nusesecure=0\n\n', 0, 0, ''),
(31, 'Free Tribe Membership', '<p>Do you have any questions that need to be answered before signing up as a <a href="http://www.anahitapolis.com/join/premium">premium tribe</a> member?</p>\r\n<p><a class="btn" href="http://www.anahitapolis.com/join/free">Join Now!</a></p>', 2, 'maintop-b', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, ''),
(32, 'Homepage Hero Unit', '<div class="hero-unit">\r\n<h1>Anahita®</h1>\r\n<p>a developer friendly and open source social networking platform and framework that helps you build the foundations of your apps and services in less time using a correct nodes-graphs-stories architecture.</p>\r\n<p><a class="btn btn-large btn-primary" href="http://www.anahitapolis.com">Learn More</a></p>\r\n</div>', 0, 'showcase-a', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=\n\n', 0, 0, ''),
(24, 'Follow Our Updates', '', 2, 'maintop-a', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 1, 'menutype=socialweb-footer\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(25, 'Developed By', '<p>Anahita® is developed by <a href="http://www.peerglobe.com" target="_blank">Peerglobe Technology</a> and <a href="http://www.rmdstudio.com" target="_blank">rmd Studio</a> who are also the co-founders of the <a href="http://www.purplerat.com" target="_blank">Purplerat Tribe of Companies</a> in Vancouver, BC, Canada.</p>', 0, 'footer-a', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 0, 'moduleclass_sfx=\n\n', 0, 0, ''),
(26, 'Bazaar Updates', '', 0, 'sidebar', 0, '0000-00-00 00:00:00', 1, 'mod_bazaar', 0, 0, 1, '', 1, 1, ''),
(27, 'Main Menu', '', 1, 'navigation', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=mainmenu\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=1\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx= nav\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, ''),
(28, 'Viewer', '', 1, 'viewer', 0, '0000-00-00 00:00:00', 1, 'mod_viewer', 0, 0, 0, 'menutype=viewer\n', 0, 0, ''),
(30, 'Premium Tribe Membership', '<p><strong>Get all the Anahita resources that you need!</strong></p>\r\n<p>Our 12 months Platinum, Gold, and Executive premium plans provide you all the resources that you need to execute and launch your social networking project or business idea.</p>\r\n<p><a class="btn btn-primary" href="http://www.anahitapolis.com/join/premium">Join Now!</a></p>', 0, 'maintop-b', 0, '0000-00-00 00:00:00', 1, 'mod_custom', 0, 0, 1, 'moduleclass_sfx=\n\n', 0, 0, '');



# --------------------------------------------------------

#
# Table structure for table `#__modules_menu`
#

CREATE TABLE `#__modules_menu` (
  `moduleid` int(11) NOT NULL default '0',
  `menuid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`moduleid`,`menuid`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

#
# Dumping data for table `#__modules_menu`
#

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

# --------------------------------------------------------

#
# Table structure for table `#__sections`
#

CREATE TABLE `#__sections` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` TEXT NOT NULL default '',
  `scope` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_scope` (`scope`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

INSERT INTO `#__sections` (`id`, `title`, `name`, `alias`, `image`, `scope`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `ordering`, `access`, `count`, `params`) VALUES
(1, 'About', '', 'about', '', 'content', 'left', '', 1, 0, '0000-00-00 00:00:00', 2, 0, 1, '');

# --------------------------------------------------------

#
# Table structure for table `#__session`
#

CREATE TABLE `#__session` (
  `username` varchar(150) default '',
  `time` varchar(14) default '',
  `session_id` varchar(200) NOT NULL default '0',
  `guest` tinyint(4) default '1',
  `userid` int(11) default '0',
  `usertype` varchar(50) default '',
  `gid` tinyint(3) unsigned NOT NULL default '0',
  `client_id` tinyint(3) unsigned NOT NULL default '0',
  `data` longtext,
  PRIMARY KEY  (`session_id`(64)),
  KEY `whosonline` (`guest`,`usertype`),
  KEY `userid` (`userid`),
  KEY `time` (`time`)
) ENGINE=InnoDB CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__stats_agents`
#

CREATE TABLE `#__stats_agents` (
  `agent` varchar(255) NOT NULL default '',
  `type` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(11) unsigned NOT NULL default '1'
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__templates_menu`
#

CREATE TABLE `#__templates_menu` (
  `template` varchar(255) NOT NULL default '',
  `menuid` int(11) NOT NULL default '0',
  `client_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`menuid`, `client_id`, `template`(255))
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# Dumping data for table `#__templates_menu`

INSERT INTO `#__templates_menu` (`template`, `menuid`, `client_id`) VALUES
('shiraz', 0, 0),
('rt_missioncontrol_j15', 0, 1);

# --------------------------------------------------------

#
# Table structure for table `#__users`
#

CREATE TABLE `#__users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `username` varchar(150) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  `usertype` varchar(25) NOT NULL default '',
  `block` tinyint(4) NOT NULL default '0',
  `sendEmail` tinyint(4) default '0',
  `gid` tinyint(3) unsigned NOT NULL default '1',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL default '',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usertype` (`usertype`),
  KEY `idx_name` (`name`),
  KEY `gid_block` (`gid`, `block`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro`
#

CREATE TABLE `#__core_acl_aro` (
  `id` int(11) NOT NULL auto_increment,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `#__section_value_value_aro` (`section_value`(100),`value`(100)),
  KEY `#__gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_map`
#

CREATE TABLE  `#__core_acl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(100) NOT NULL,
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_groups`
#
CREATE TABLE `#__core_acl_aro_groups` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `#__gacl_parent_id_aro_groups` (`parent_id`),
  KEY `#__gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

#
# Dumping data for table `#__core_acl_aro_groups`
#

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

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_groups_aro_map`
#
CREATE TABLE `#__core_acl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '',
  `aro_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_id_aro_id_groups_aro_map` (`group_id`,`section_value`,`aro_id`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

# --------------------------------------------------------

#
# Table structure for table `#__core_acl_aro_sections`
#
CREATE TABLE `#__core_acl_aro_sections` (
  `id` int(11) NOT NULL auto_increment,
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `#__gacl_value_aro_sections` (`value`),
  KEY `#__gacl_hidden_aro_sections` (`hidden`)
) ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;

INSERT INTO `#__core_acl_aro_sections` VALUES (10,'users',1,'Users',0);
