<?php
/**
* @version		$Id: admin.cpanel.php 18130 2010-07-14 11:21:35Z louis $
* @package		Joomla
* @subpackage	Admin
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JApplicationHelper::getPath( 'admin_html' ) );

switch (JRequest::getCmd('task'))
{
	default:
	{
		// Check to see if the mootools upgrade plugin is enabled.
		if (!JPluginHelper::getPlugin('system', 'mtupgrade')) {

			// Only do the mtupgrade plugin check once per session.
			$app = &JFactory::getApplication();
			if (!$app->getUserState('com_cpanel.mtupgrade.checked')) {

				// If it is not enabled, let's check to see if it is even installed.
				$db = &JFactory::getDBO();
				$db->setQuery(
					'SELECT id' .
					' FROM #__plugins' .
					' WHERE folder = "system"' .
					' AND element = "mtupgrade"'
				);
				$exists = (bool) $db->loadResult();

				// If it is not installed, install it as disabled and raise a notice.
				if (!$exists) {
					$plg = & JTable::getInstance('plugin');
					$plg->name = 'System - Mootools Upgrade';
					$plg->ordering = 0;
					$plg->folder = 'system';
					$plg->iscore = 0;
					$plg->access = 0;
					$plg->client_id = 0;
					$plg->element = 'mtupgrade';
					$plg->published = 0;

					// If unable to store the plugin, raise a notice.
					if (!$plg->store()) {
						JError::raiseNotice(500, $plg->getError() /*JText::sprintf('Unable to auto-install the Mootools Upgrade plugin.', $plg->getError())*/);
					}
					// Show a message stating that the plugin is now available.
					else {
						//$app->enqueueMessage(JText::_('Mootools Upgrade plugin available.'));
					}
				}

				// Set the mtupgrade plugin checked flag for the session.
				$app->setUserState('com_cpanel.mtupgrade.checked', true);
			}


		}

		//set the component specific template file in the request
		JRequest::setVar('tmpl', 'cpanel');
		HTML_cpanel::display();
	}	break;
}