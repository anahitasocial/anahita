<?php
/**
 * @package   Installer Bundle Framework - RocketTheme
 * @version   1.5.2 June 9, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Installer uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if (!class_exists( "JInstallerModule")){
    jimport('joomla.installer.adapters.module');
}
/**
 * Module installer
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 */
class RokInstallerModule extends JInstallerModule
{
	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function install()
	{
		// Get a database connector object
		$db =& $this->parent->getDBO();

		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =& $manifest->document;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$this->set('name', $name);

		// Get the component description
		$description = & $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Target Application Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Get the target application
		if ($cname = $this->manifest->attributes('client')) {
			// Attempt to map the client to a base path
			jimport('joomla.application.helper');
			$client =& JApplicationHelper::getClientInfo($cname, true);
			if ($client === false) {
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Unknown client type').' ['.$client->name.']');
				return false;
			}
			$basePath = $client->path;
			$clientId = $client->id;
		} else {
			// No client attribute was found so we assume the site as the client
			$cname = 'site';
			$basePath = JPATH_SITE;
			$clientId = 0;
		}

		// Set the installation path
		$element =& $this->manifest->getElementByPath('files');
		if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
			$files = $element->children();
			foreach ($files as $file) {
				if ($file->attributes('module')) {
					$mname = $file->attributes('module');
					break;
				}
			}
		}
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $basePath.DS.'modules'.DS.$mname);
		} else {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('No module file specified'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		/*
		 * If the module directory already exists, then we will assume that the
		 * module is already installed or another module is using that
		 * directory.
		 */
		if (file_exists($this->parent->getPath('extension_root'))&&!$this->parent->getOverwrite()) {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Another module is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the module directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		 * Since we created the module directory and will want to remove it if
		 * we have to roll back the installation, lets add it to the
		 * installation step stack
		 */
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Parse optional tags
		$this->parent->parseMedia($this->manifest->getElementByPath('media'), $clientId);
		$this->parent->parseLanguages($this->manifest->getElementByPath('languages'), $clientId);

		// Parse deprecated tags
		$this->parent->parseFiles($this->manifest->getElementByPath('images'), -1);

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Check to see if a module by the same name is already installed
		$query = 'SELECT `id`' .
				' FROM `#__modules` ' .
				' WHERE module = '.$db->Quote($mname) .
				' AND client_id = '.(int)$clientId;
		$db->setQuery($query);
		if (!$db->Query()) {
			// Install failed, roll back changes
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();

 		// load module instance
 		$row =& JTable::getInstance('module');

		// Was there a module already installed with the same name?
		// If there was then we wouldn't be here because it would have
		// been stopped by the above. Otherwise the files weren't there
		// (e.g. migration) or its an upgrade (files overwritten)
		// So all we need to do is create an entry when we can't find one

		if ($id) {
			$row->load($id);
		} else {
			$row->title = JText::_($this->get('name'));
			$row->ordering = $row->getNextOrder( "position='left'" );
			$row->position = 'left';
			$row->showtitle = 1;
			$row->iscore = 0;
			$row->access = $clientId == 1 ? 2 : 0;
			$row->client_id = $clientId;
			$row->module = $mname;
			$row->params = $this->parent->getParams();

			if (!$row->store()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}

			// Since we have created a module item, we add it to the installation step stack
			// so that if we have to rollback the changes we can undo it.
			$this->parent->pushStep(array ('type' => 'module', 'id' => $row->id));

			// Clean up possible garbage first
			$query = 'DELETE FROM #__modules_menu WHERE moduleid = '.(int) $row->id;
			$db->setQuery( $query );
			if (!$db->query()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}

			// Time to create a menu entry for the module
			$query = 'INSERT INTO `#__modules_menu` ' .
					' VALUES ('.(int) $row->id.', 0 )';
			$db->setQuery($query);
			if (!$db->query()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}

			/*
			 * Since we have created a menu item, we add it to the installation step stack
			 * so that if we have to rollback the changes we can undo it.
			 */
			$this->parent->pushStep(array ('type' => 'menu', 'id' => $db->insertid()));
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}

		// Load module language file
		$lang =& JFactory::getLanguage();
		$lang->load($row->module, JPATH_BASE.DS.'..');

		return true;
	}
}
