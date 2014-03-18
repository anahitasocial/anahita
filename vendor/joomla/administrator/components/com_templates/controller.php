<?php
/**
 * @version		$Id: controller.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Templates
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * @package		Joomla
 * @subpackage	Templates
 */
class TemplatesController
{
	/**
	* Compiles a list of installed, version 4.5+ templates
	*
	* Based on xml files found.  If no xml file found the template
	* is ignored
	*/
	public static function viewTemplates()
	{
		global $mainframe, $option;

		// Initialize some variables
		$db		=& JFactory::getDBO();
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		// Initialize the pagination variables
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.'.$client->id.'.limitstart', 'limitstart', 0, 'int');

		$select[] 			= JHTML::_('select.option', '0', JText::_('Site'));
		$select[] 			= JHTML::_('select.option', '1', JText::_('Administrator'));
		$lists['client'] 	= JHTML::_('select.genericlist',  $select, 'client', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $client->id);

		$tBaseDir = $client->path.DS.'templates';

		//get template xml file info
		$rows = array();
		$rows = TemplatesHelper::parseXMLTemplateFiles($tBaseDir);

		// set dynamic template information
		for($i = 0; $i < count($rows); $i++)  {
			$rows[$i]->assigned		= TemplatesHelper::isTemplateAssigned($rows[$i]->directory);
			$rows[$i]->published	= TemplatesHelper::isTemplateDefault($rows[$i]->directory, $client->id);
		}

		jimport('joomla.html.pagination');
		$page = new JPagination(count($rows), $limitstart, $limit);

		$rows = array_slice($rows, $page->limitstart, $page->limit);

		require_once (JPATH_COMPONENT.DS.'admin.templates.html.php');
		TemplatesView::showTemplates($rows, $lists, $page, $option, $client);
	}

	public static function editTemplate()
	{
		jimport('joomla.filesystem.path');

		// Initialize some variables
		$db			= & JFactory::getDBO();
		$cid		= JRequest::getVar('cid', array(), 'method', 'array');
		$cid		= array(JFilterInput::clean(@$cid[0], 'cmd'));
		$template	= $cid[0];
		$option		= JRequest::getCmd('option');
		$client		=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		if (!$cid[0]) {
			return JError::raiseWarning( 500, JText::_('Template not specified') );
		}

		$tBaseDir	= JPath::clean($client->path.DS.'templates');

		if (!is_dir( $tBaseDir . DS . $template )) {
			return JError::raiseWarning( 500, JText::_('Template not found') );
		}
		$lang =& JFactory::getLanguage();
		$lang->load( 'tpl_'.$template, JPATH_ADMINISTRATOR );

		$ini	= $client->path.DS.'templates'.DS.$template.DS.'params.ini';
		$xml	= $client->path.DS.'templates'.DS.$template.DS.'templateDetails.xml';
		$row	= TemplatesHelper::parseXMLTemplateFile($tBaseDir, $template);

		jimport('joomla.filesystem.file');
		// Read the ini file
		if (JFile::exists($ini)) {
			$content = JFile::read($ini);
		} else {
			$content = null;
		}

		$params = new JParameter($content, $xml, 'template');

		$assigned = TemplatesHelper::isTemplateAssigned($row->directory);
		$default = TemplatesHelper::isTemplateDefault($row->directory, $client->id);

		if($client->id == '1')  {
			$lists['selections'] =  JText::_('Cannot assign an administrator template');
		} else {
			$lists['selections'] = TemplatesHelper::createMenuList($template);
		}

		if ($default) {
			$row->pages = 'all';
		} elseif (!$assigned) {
			$row->pages = 'none';
		} else {
			$row->pages = null;
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');

		require_once (JPATH_COMPONENT.DS.'admin.templates.html.php');
		TemplatesView::editTemplate($row, $lists, $params, $option, $client, $ftp, $template);
	}

	public static function saveTemplate()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize some variables
		$db			 = & JFactory::getDBO();

		$template	= JRequest::getVar('id', '', 'method', 'cmd');
		$option		= JRequest::getVar('option', '', '', 'cmd');
		$client		=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$menus		= JRequest::getVar('selections', array(), 'post', 'array');
		$params		= JRequest::getVar('params', array(), 'post', 'array');
		$default	= JRequest::getBool('default');
		JArrayHelper::toInteger($menus);

		if (!$template) {
			$mainframe->redirect('index.php?option='.$option.'&client='.$client->id, JText::_('Operation Failed').': '.JText::_('No template specified.'));
		}

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');

		$file = $client->path.DS.'templates'.DS.$template.DS.'params.ini';

		jimport('joomla.filesystem.file');
		if ( count($params) )
		{
			$registry = new JRegistry();
			$registry->loadArray($params);
			$txt    = $registry->toString();
			$return = file_put_contents($file, $txt);
			if (!$return) {
				$mainframe->redirect('index.php?option='.$option.'&client='.$client->id, JText::_('Operation Failed').': '.JText::sprintf('Failed to open file for writing.', $file));		
			}
		}

		// Reset all existing assignments
		$query = 'DELETE FROM #__templates_menu' .
				' WHERE client_id = 0' .
				' AND template = '.$db->Quote( $template );
		$db->setQuery($query);
		$db->query();

		if ($default) {
			$menus = array( 0 );
		}

		foreach ($menus as $menuid)
		{
			// If 'None' is not in array
			if ((int) $menuid >= 0)
			{
				// check if there is already a template assigned to this menu item
				$query = 'DELETE FROM #__templates_menu' .
						' WHERE client_id = 0' .
						' AND menuid = '.(int) $menuid;
				$db->setQuery($query);
				$db->query();

				$query = 'INSERT INTO #__templates_menu' .
						' SET client_id = 0, template = '. $db->Quote( $template ) .', menuid = '.(int) $menuid;
				$db->setQuery($query);
				$db->query();
			}
		}

		$task = JRequest::getCmd('task');
		if($task == 'apply') {
			$mainframe->redirect('index.php?option='.$option.'&task=edit&cid[]='.$template.'&client='.$client->id);
		} else {
			$mainframe->redirect('index.php?option='.$option.'&client='.$client->id);
		}
	}

	public static function cancelTemplate()
	{
		global $mainframe;

		// Initialize some variables
		$option	= JRequest::getCmd('option');
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		$mainframe->redirect('index.php?option='.$option.'&client='.$client->id);
	}
}
