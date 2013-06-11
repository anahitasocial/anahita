<?php
/**
* @version		$Id:template.php 6961 2007-03-15 16:06:53Z tcp $
* @package		Joomla.Framework
* @subpackage	Installer
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if (!class_exists( "JInstallerComponent")){
    jimport('joomla.installer.adapters.template');
}
/**
 * Template installer
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 */
class RokInstallerTemplate extends JInstallerTemplate
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
		// Get database connector object
		$db =& $this->parent->getDBO();
		$manifest =& $this->parent->getManifest();
		$root =& $manifest->document;

		// Get the client application target
		if ($cname = $root->attributes('client')) {
			// Attempt to map the client to a base path
			jimport('joomla.application.helper');
			$client =& JApplicationHelper::getClientInfo($cname, true);
			if ($client === false) {
				$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Unknown client type').' ['.$cname.']');
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

		// Set the extensions name
		$name =& $root->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'cmd');
		$this->set('name', $name);

		// Set the template root path
		$this->parent->setPath('extension_root', $basePath.DS.'templates'.DS.strtolower(str_replace(" ", "_", $this->get('name'))));

		/*
		 * If the template directory already exists, then we will assume that the template is already
		 * installed or another template is using that directory.
		 */
		if (file_exists($this->parent->getPath('extension_root')) && !$this->parent->getOverwrite()) {
			JError::raiseWarning(100, JText::_('Template').' '.JText::_('Install').': '.JText::_('Another template is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the template directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Failed to create directory').' "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		// If we created the template directory and will want to remove it if we have to roll back
		// the installation, lets add it to the installation step stack
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

        $exceptions = $root->getElementByPath('exceptions');
        if ($exceptions !== false && $this->parent->prepExceptions($exceptions, -1) === false) {
			// Install failed, rollback changes
			$this->parent->abort();
			return false;
		} 
		// Copy all the necessary files
		if ($this->parent->parseFiles($root->getElementByPath('files'), -1) === false) {
			// Install failed, rollback changes
			$this->parent->abort();
			return false;
		}
		if ($this->parent->parseFiles($root->getElementByPath('images'), -1) === false) {
			// Install failed, rollback changes
			$this->parent->abort();
			return false;
		}
		if ($this->parent->parseFiles($root->getElementByPath('css'), -1) === false) {
			// Install failed, rollback changes
			$this->parent->abort();
			return false;
		}

		// Parse optional tags
		$this->parent->parseFiles($root->getElementByPath('media'), $clientId);
		$this->parent->parseLanguages($root->getElementByPath('languages'));
		$this->parent->parseLanguages($root->getElementByPath('administration/languages'), 1);

		// Get the template description
		$description = & $root->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->parent->abort(JText::_('Template').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}

		// Load template language file
		$lang =& JFactory::getLanguage();
		$lang->load('tpl_'.$name);

        $this->parent->finishExceptions();
        
		return true;
	}
}
