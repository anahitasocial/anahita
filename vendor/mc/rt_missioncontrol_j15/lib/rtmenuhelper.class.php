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
		$canConfig			= $user->authorize('com_config', 'manage');
		$manageTemplates	= $user->authorize('com_templates', 'manage');
		$manageLanguages	= $user->authorize('com_languages', 'manage');
		$installPlugins		= $user->authorize('com_installer', 'plugin');
		$editAllPlugins		= $user->authorize('com_plugins', 'manage');
		$installComponents	= $user->authorize('com_installer', 'component');
		$editAllComponents	= $user->authorize('com_components', 'manage');
		$canManageUsers		= $user->authorize('com_users', 'manage');

		/*
		 * Get the menu object
		 */
		require_once ($mctrl->templatePath.DS.'lib'.DS.'rtmenu.class.php');
		$menu = new RTAdminCSSMenu();
		$menu->init($this->menudata);
		
		/*
		 * Dashboard
		 */
		$menu->addChild(new JMenuNode(JText::_('Dashboard'), 'index.php?', 'dashboard'));
		
		/*
		 * Manage Users
		 */
		if ($canManageUsers) {
			$menu->addChild(new JMenuNode(JText::_('Users'), 'index.php?option=com_users&task=view', 'users'));
		}

		/*
		 * Extend SubMenu
		 */
		if ($editAllPlugins) 
		{
			$menu->addChild(new JMenuNode(JText::_('Extend')), true);
		
			if ($editAllPlugins)
				$menu->addChild(new JMenuNode(JText::_('Plugin Manager'), 'index.php?option=com_plugins', 'plugin'));
			
			if ($manageTemplates) 
			{
				$menu->addChild(new JMenuNode(JText::_('Template Manager'), 'index.php?option=com_templates', 'themes'),true);
				$menu->getParent();
			}
			
			if ($manageLanguages)
				$menu->addChild(new JMenuNode(JText::_('Language Manager'), 'index.php?option=com_languages', 'language'));
		}
		
		if ($editAllComponents)
		{
			$menu->addSeparator();
			
			$query = 'SELECT *' .
				' FROM #__components' .
				' WHERE enabled = 1' .
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
		
		if ($editAllPlugins || $editAllComponents) {
			$menu->getParent();
		}
		
		if ($canConfig) {
			$menu->addChild(new JMenuNode(JText::_('Configure'), 'index.php?option=com_config', 'config'));
		}

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

		// Extend SubMenu
		if ($installComponents || $editAllPlugins) {
			$menu->addChild(new JMenuNode(JText::_('Extend'), null, 'disabled daddy'));
		}

		// System SubMenu
		if ($canConfig) {
			$menu->addChild(new JMenuNode(JText::_('Configure'),  null, 'disabled'));
		}

		$menu->renderMenu('menu', 'menutop level1 disabled');
	}
	
	function __construct() {
		// menu data
		$menus['Users'] = array('com_users');
		$menus['Extend'] = array('com_plugins','com_templates','com_languages');
		$menus['Config'] = array('com_config');
		$menus['Tools'] = array('com_checkin','com_cache');
		
		$this->menudata = $menus;
	}
	



}