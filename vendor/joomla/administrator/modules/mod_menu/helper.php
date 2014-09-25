<?php
/**
* @version		$Id:mod_menu.php 2463 2006-02-18 06:05:38Z webImagery $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__).DS.'menu.php');

class modMenuHelper
{
	/**
	 * Show the menu
	 * @param string The current user type
	 */
	function buildMenu()
	{
		global $mainframe;

		$lang		= & JFactory::getLanguage();
		$user		= & JFactory::getUser();
		$db			= & JFactory::getDBO();
		$usertype	= $user->get('usertype');

		// cache some acl checks
		$canConfig			= $user->authorize('com_config', 'manage');
		$manageTemplates	= $user->authorize('com_templates', 'manage');
		$manageLanguages	= $user->authorize('com_languages', 'manage');
		$editAllModules		= $user->authorize('com_modules', 'manage');
		$installPlugins		= $user->authorize('com_installer', 'plugin');
		$editAllPlugins		= $user->authorize('com_plugins', 'manage');
		$editAllComponents	= $user->authorize('com_components', 'manage');
		$canManageUsers		= $user->authorize('com_users', 'manage');

		/*
		 * Get the menu object
		 */
		$menu = new JAdminCSSMenu();

		/*
		 * Site SubMenu
		 */
		$menu->addChild(new JMenuNode(JText::_('Site')), true);
		$menu->addChild(new JMenuNode(JText::_('Control Panel'), 'index.php', 'class:cpanel'));
		$menu->addSeparator();
		if ($canManageUsers) {
			$menu->addChild(new JMenuNode(JText::_('User Manager'), 'index.php?option=com_users&task=view', 'class:user'));
		}		
		$menu->addSeparator();
		if ($canConfig) {
			$menu->addChild(new JMenuNode(JText::_('Configuration'), 'index.php?option=com_config', 'class:config'));
			$menu->addSeparator();
		}
		$menu->addChild(new JMenuNode(JText::_('Logout'), 'index.php?option=com_login&task=logout', 'class:logout'));

		$menu->getParent();

		/*
		 * Extensions SubMenu
		 */
		if ($installModules)
		{
			$menu->addChild(new JMenuNode(JText::_('Extensions')), true);

			$menu->addChild(new JMenuNode(JText::_('Install/Uninstall'), 'index.php?option=com_installer', 'class:install'));
			$menu->addSeparator();
			if ($editAllModules) {
				$menu->addChild(new JMenuNode(JText::_('Module Manager'), 'index.php?option=com_modules', 'class:module'));
			}
			if ($editAllPlugins) {
				$menu->addChild(new JMenuNode(JText::_('Plugin Manager'), 'index.php?option=com_plugins', 'class:plugin'));
			}
			if ($manageTemplates) {
				$menu->addChild(new JMenuNode(JText::_('Template Manager'), 'index.php?option=com_templates', 'class:themes'));
			}
			if ($manageLanguages) {
				$menu->addChild(new JMenuNode(JText::_('Language Manager'), 'index.php?option=com_languages', 'class:language'));
			}
			$menu->getParent();
		}

		$menu->renderMenu('menu', '');
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
		$canMassMail			= $user->authorize('com_massmail', 'manage');
		$canManageUsers		= $user->authorize('com_users', 'manage');

		$text = JText::_('Menu inactive for this Page', true);

		// Get the menu object
		$menu = new JAdminCSSMenu();

		// Site SubMenu
		$menu->addChild(new JMenuNode(JText::_('Site'), null, 'disabled'));
		
		// Components SubMenu
		if ($installComponents) {
			$menu->addChild(new JMenuNode(JText::_('Components'), null, 'disabled'));
		}

		// Extensions SubMenu
		if ($installModules) {
			$menu->addChild(new JMenuNode(JText::_('Extensions'), null, 'disabled'));
		}

		$menu->renderMenu('menu', 'disabled');
	}
}
?>
