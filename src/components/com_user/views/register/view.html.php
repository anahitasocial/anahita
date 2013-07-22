<?php
/**
* @version		$Id: view.html.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Registration
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Registration component
 *
 * @package		Joomla
 * @subpackage	Registration
 * @since 1.0
 */
class UserViewRegister extends JView
{
	function display($tpl = null)
	{
		global $mainframe;

		// Check if registration is allowed
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if (!$usersConfig->get( 'allowUserRegistration' )) {
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		$pathway  =& $mainframe->getPathway();
		$document =& JFactory::getDocument();
		$params	= &$mainframe->getParams();

	 	// Page Title
		$menus	= &JSite::getMenu();
		$menu	= $menus->getActive();

		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Registration' ));
			}
		} else {
			$params->set('page_title',	JText::_( 'Registration' ));
		}
		$document->setTitle( $params->get( 'page_title' ) );

		$pathway->addItem( JText::_( 'New' ));

		// Load the form validation behavior
		JHTML::_('behavior.formvalidation');

		$user =& JFactory::getUser();
		$this->assignRef('user', $user);
		$this->assignRef('params',		$params);
		parent::display($tpl);
	}
}
