<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

/**
 * @package   missioncontrol
 * @subpackage lib
 */
class RTMenuHelper {

	var $menudata;

	/**
	 * Show the menu
	 * @param string The current user type
	 */
	function buildMenu()
	{
		global $mainframe;
        global $mctrl;

		$lang		= & JFactory::getLanguage();
		$user		= & JFactory::getUser();
		$db			= & JFactory::getDBO();
		$usertype	= $user->get('usertype');

		// cache some acl checks
		$canCheckin			= $user->authorize('com_checkin', 'manage');
		$canConfig			= $user->authorize('com_config', 'manage');
		$manageTemplates	= $user->authorize('com_templates', 'manage');
		$manageTrash		= $user->authorize('com_trash', 'manage');
		$manageMenuMan		= $user->authorize('com_menus', 'manage');
		$manageLanguages	= $user->authorize('com_languages', 'manage');
		$installModules		= $user->authorize('com_installer', 'module');
		$editAllModules		= $user->authorize('com_modules', 'manage');
		$installPlugins		= $user->authorize('com_installer', 'plugin');
		$editAllPlugins		= $user->authorize('com_plugins', 'manage');
		$installComponents	= $user->authorize('com_installer', 'component');
		$editAllComponents	= $user->authorize('com_components', 'manage');
		$canManageUsers		= $user->authorize('com_users', 'manage');

		// Menu Types
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'helper.php' );
		$menuTypes 	= MenusHelper::getMenuTypelist();

		/*
		 * Get the menu object
		 */
		require_once ($mctrl->templatePath.DS.'lib'.DS.'rtmenu.class.php');
		$menu = new RTAdminCSSMenu();
		$menu->init($this->menudata);
		
		/*
		 * Dashboard
		 */
		$menu->addChild(new JMenuNode(JText::_('Dashboard'), 'index.php', 'dashboard'));
		
		/*
		 * Manage Users
		 */
		if ($canManageUsers) {
			$menu->addChild(new JMenuNode(JText::_('Users'), 'index.php?option=com_users&task=view', 'users'));
		}


		/*
		 * Content SubMenu
		 */
		$menu->addChild(new JMenuNode(JText::_('Articles')), true);
		$menu->addChild(new JMenuNode(JText::_('Article Manager'), 'index.php?option=com_content', 'article'));
		if ($manageTrash) {
			$menu->addChild(new JMenuNode(JText::_('Article Trash'), 'index.php?option=com_trash&task=viewContent', 'trash'));
		}
		$menu->addSeparator();
		$menu->addChild(new JMenuNode(JText::_('Section Manager'), 'index.php?option=com_sections&scope=content', 'section'));
		$menu->addChild(new JMenuNode(JText::_('Category Manager'), 'index.php?option=com_categories&section=com_content', 'category'));
		$menu->addSeparator();
		$menu->addChild(new JMenuNode(JText::_('Frontpage Manager'), 'index.php?option=com_frontpage', 'frontpage'));

		$menu->getParent();
		
		/*
		 * Menus SubMenu
		 */
		$menu->addChild(new JMenuNode(JText::_('Menus')), true);
		if ($manageMenuMan) {
			$menu->addChild(new JMenuNode(JText::_('Menu Manager'), 'index.php?option=com_menus', 'menu'));
		}
		if ($manageTrash) {
			$menu->addChild(new JMenuNode(JText::_('Menu Trash'), 'index.php?option=com_trash&task=viewMenu', 'trash'));
		}

		if($manageTrash || $manageMenuMan) {
			$menu->addSeparator();
		}
		/*
		 * SPLIT HR
		 */
		if (count($menuTypes)) {
			foreach ($menuTypes as $menuType) {
				$menu->addChild(
					new JMenuNode(
						$menuType->title . ($menuType->home ? ' <span class="default">*</span>' : ''), 
						'index.php?option=com_menus&task=view&menutype='
						. $menuType->menutype,
						'menu'
					)
				);
			}
		}

		$menu->getParent();

		/*
		 * Extend SubMenu
		 */
		if ($installModules || $editAllComponents) {
			$menu->addChild(new JMenuNode(JText::_('Extend')), true);
		} 

		if ($installModules)
		{
			//$menu->addChild(new JMenuNode(JText::_('Anahita Bazaar'), 'index.php?option=com_socialengine&view=bazaar', 'install'));
			$menu->addChild(new JMenuNode(JText::_('Install/Uninstall'), 'index.php?option=com_installer', 'install'));
			$menu->addSeparator();
			if ($editAllModules) {
				$menu->addChild(new JMenuNode(JText::_('Module Manager'), 'index.php?option=com_modules', 'module'),true);
				$menu->addChild(new JMenuNode(JText::_('Site Modules'),'index.php?option=com_modules&client=0'));
				$menu->addChild(new JMenuNode(JText::_('Admin Modules'),'index.php?option=com_modules&client=1'));
				$menu->getParent();
			}
			if ($editAllPlugins) {
				$menu->addChild(new JMenuNode(JText::_('Plugin Manager'), 'index.php?option=com_plugins', 'plugin'));
			}
			if ($manageTemplates) {
				$menu->addChild(new JMenuNode(JText::_('Template Manager'), 'index.php?option=com_templates', 'themes'),true);
				$menu->addChild(new JMenuNode(JText::_('Site Templates'),'index.php?option=com_templates&client=0'));
				$menu->addChild(new JMenuNode(JText::_('Admin Templates'),'index.php?option=com_templates&client=1'));
				$menu->getParent();
			}
			if ($manageLanguages) {
				$menu->addChild(new JMenuNode(JText::_('Language Manager'), 'index.php?option=com_languages', 'language'));
			}
		}
		
		if ($installModules && $editAllComponents) {
			$menu->addSeparator();
		}
		
		if ($editAllComponents)
		{

			$query = 'SELECT *' .
				' FROM #__components' .
				' WHERE '.$db->NameQuote( 'option' ).' <> "com_frontpage"' .
				' AND enabled = 1' .
				' ORDER BY ordering, name';
			$db->setQuery($query);
			$comps = $db->loadObjectList(); // component list
			$subs = array(); // sub menus
			$langs = array(); // additional language files to load

			// first pass to collect sub-menu items
			foreach ($comps as $row)
			{
				if ($row->parent)
				{
					if (!array_key_exists($row->parent, $subs)) {
						$subs[$row->parent] = array ();
					}
					$subs[$row->parent][] = $row;
					$langs[$row->option.'.menu'] = true;
				} elseif (trim($row->admin_menu_link)) {
					$langs[$row->option.'.menu'] = true;
				}
			}

			// Load additional language files
			if (array_key_exists('.menu', $langs)) {
				unset($langs['.menu']);
			}
			foreach ($langs as $lang_name => $nothing) {
				$lang->load($lang_name);
			}

			foreach ($comps as $row)
			{
				if ($editAllComponents | $user->authorize('administration', 'edit', 'components', $row->option))
				{
					if ($row->parent == 0 && (trim($row->admin_menu_link) || array_key_exists($row->id, $subs)))
					{
						$text = $lang->hasKey($row->option) ? JText::_($row->option) : $row->name;
						$link = $row->admin_menu_link ? "index.php?$row->admin_menu_link" : "index.php?option=$row->option";
						if (array_key_exists($row->id, $subs)) {
							$menu->addChild(new JMenuNode($text, $link, $row->admin_menu_img), true);
							foreach ($subs[$row->id] as $sub) {
								$key  = $row->option.'.'.$sub->name;
								$text = $lang->hasKey($key) ? JText::_($key) : $sub->name;
								$link = $sub->admin_menu_link ? "index.php?$sub->admin_menu_link" : null;
								$menu->addChild(new JMenuNode($text, $link, $sub->admin_menu_img));
							}
							$menu->getParent();
						} else {
							$menu->addChild(new JMenuNode($text, $link, $row->admin_menu_img));
						}
					}
				}
			}
		}
		
		if ($installModules || $editAllComponents) {
			$menu->getParent();
		}
		
		if ($canConfig) {
			$menu->addChild(new JMenuNode(JText::_('Configure'), 'index.php?option=com_config', 'config'));
		}

		/*
		 * Help SubMenu
		 */
		$menu->addChild(new JMenuNode(JText::_('Help')), true);
		$menu->addChild(new JMenuNode(JText::_('Anahita Help'), 'http://www.anahitapolis.com', 'help'));
		$menu->addChild(new JMenuNode(JText::_('System Info'), 'index.php?option=com_admin&task=sysinfo', 'info'));

		$menu->getParent();

		$menu->renderMenu('mctrl-menu', 'menutop level1');
	}

	/**
	 * Show an disbaled version of the menu, used in edit pages
	 *
	 * @param string The current user type
	 */
	function buildDisabledMenu()
	{
		$lang	 =& JFactory::getLanguage();
		$user	 =& JFactory::getUser();
		$usertype = $user->get('usertype');

		$canConfig			= $user->authorize('com_config', 'manage');
		$installModules		= $user->authorize('com_installer', 'module');
		$editAllModules		= $user->authorize('com_modules', 'manage');
		$installPlugins		= $user->authorize('com_installer', 'plugin');
		$editAllPlugins		= $user->authorize('com_plugins', 'manage');
		$installComponents	= $user->authorize('com_installer', 'component');
		$editAllComponents	= $user->authorize('com_components', 'manage');
		$canManageUsers		= $user->authorize('com_users', 'manage');

		$text = JText::_('Menu inactive for this Page', true);

		// Get the menu object
		require_once ('rtmenu.class.php');
		$menu = new RTAdminCSSMenu();
		$menu->init($this->menudata);

		// Dashboard SubMenu
		$menu->addChild(new JMenuNode(JText::_('Dashboard'), null, 'disabled'));

		// Users SubMenu
		if ($canManageUsers) {
			$menu->addChild(new JMenuNode(JText::_('Users'), null, 'disabled'));
		}

		// Articles SubMenu
		$menu->addChild(new JMenuNode(JText::_('Articles'), null, 'disabled daddy'));
		
		// Menus SubMenu
		$menu->addChild(new JMenuNode(JText::_('Menus'), null, 'disabled daddy'));

		// Extend SubMenu
		if ($installComponents || $installModules) {
			$menu->addChild(new JMenuNode(JText::_('Extend'), null, 'disabled daddy'));
		}

		// System SubMenu
		if ($canConfig) {
			$menu->addChild(new JMenuNode(JText::_('Configure'),  null, 'disabled'));
		}

		// Help SubMenu
		$menu->addChild(new JMenuNode(JText::_('Help'),  null, 'disabled daddy'));

		$menu->renderMenu('menu', 'menutop level1 disabled');
	}
	
	function __construct() {
		// menu data
		$menus['Dashboard'] = array('com_content','com_trash:task=viewContent','com_sections:scope=content','com_categories:section=com_content','com_frontpage');
		$menus['Articles'] = array('com_menus','com_trash:task=viewMenu');
		$menus['Users'] = array('com_users');
		$menus['Extend'] = array('com_installer','com_modules','com_plugins','com_templates','com_languages','com_search');
		$menus['Config'] = array('com_config');
		$menus['Help'] = array('com_admin:task=help','com_admin:task=sysinfo');
		$menus['Tools'] = array('com_checkin','com_cache');
		
		$this->menudata = $menus;
	}
	



}