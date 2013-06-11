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

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');

/**
 * Joomla base installer class
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 */
class RokStarInstaller extends JInstaller
{

    var $no_overwrite = array();
    var $backup_dir;
    const EXCEPTION_NO_REPLACE = 'noreplace';

    /**
	 * Returns a reference to the global Installer object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @return	object	An installer object
	 * @since 1.5
	 */
	function &getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new RokStarInstaller();
		}
		return $instance;
	}

    /**
	 * Constructor
	 *
	 * @access protected
	 */
	function __construct()
	{
		parent::__construct();

	}

    
	/**
	 * Set an installer adapter by name
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	function setAdapter($name, $adapter = null)
	{
		$ret = parent::setAdapter($name, $adapter);
		if (!$ret){
			return $ret;
		}
		if (!is_object($adapter))
		{
			// Try to load the adapter object
			$adapter_file= dirname(__FILE__).DS.'adapters'.DS.strtolower($name).'.php';
			if (JFile::exists($adapter_file)){ 
				@include_once($adapter_file);
				$class = 'RokInstaller'.ucfirst($name);
				if (!class_exists($class)) {
					return false;
				}

				$adapter = new $class($this);
				$adapter->parent =& $this;
                $this->_adapters[strtolower($name)] = &$adapter;
			}	
		}
		return true;
	}

	/**
	 * Method to parse through a files element of the installation manifest and take appropriate
	 * action.
	 *
	 * @access	public
	 * @param	object	$element 	The xml node to process
	 * @param	int		$cid		Application ID of application to install to
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function prepExceptions($element, $cid=0)
	{
        $config =& JFactory::getConfig();
        $this->backup_dir = $config->getValue('config.tmp_path') . DS.uniqid('backup_');
        if (!JFolder::create($this->backup_dir)) {
            JError::raiseWarning(1, 'JInstaller::install: '.JText::_('Failed to create directory').' "'.$this->backup_dir.'"');
            return false;
        }

		// Initialize variables
		$exceptionFiles = array ();

		// Get the client info
		jimport('joomla.application.helper');
		$client =& JApplicationHelper::getClientInfo($cid);

		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return 0;
		}

		// Get the array of file nodes to process
		$files = $element->children();
		if (count($files) == 0) {
			// No files to process
			return 0;
		}

		/*
		 * Here we set the folder we are going to remove the files from.
		 */
		if ($client) {
			$pathname = 'extension_'.$client->name;
			$destination = $this->getPath($pathname);
		} else {
			$pathname = 'extension_root';
			$destination = $this->getPath($pathname);
		}

		/*
		 * Here we set the folder we are going to copy the files from.
		 *
		 * Does the element have a folder attribute?
		 *
		 * If so this indicates that the files are in a subdirectory of the source
		 * folder and we should append the folder attribute to the source path when
		 * copying files.
		 */
		if ($folder = $element->attributes('folder')) {
			$source = $this->getPath('source').DS.$folder;
		} else {
			$source = $this->getPath('source');
		}

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
            $exception_type = $file->attributes('type');
            $current_file =$destination.DS.$file->data();
            if ($exception_type == self::EXCEPTION_NO_REPLACE && file_exists($current_file))
            {
                $type = ( $file->name() == 'folder') ? 'folder' : 'file';

                $backuppath['src']	= $current_file;
			    $backuppath['dest']	= $this->backup_dir.DS.$file->data();
                $backuppath['type'] = $type;

                $replacepath['src'] =  $backuppath['dest'];
                $replacepath['dest'] = $backuppath['src'];
                $replacepath['type'] = $type;

                $this->no_overwrite[] = $replacepath;
                if (!$this->copyFiles(array($backuppath))){
                    JError::raiseWarning(1, 'JInstaller::install: '.JText::_('Failed to copy backup to ').' "'.$backuppath['dest'].'"');
                    return false;
                }
            }
		}
        return true;
	}

    function finishExceptions(){
        if (($this->upgrade && !empty($this->no_overwrite)) || !$this->upgrade ){
            foreach ($this->no_overwrite as $restore){
                if (JPath::canChmod($restore['dest'])){
                    JPath::setPermissions($restore['dest']);
                }
            }

            if ($this->copyFiles($this->no_overwrite)){
                JFolder::delete($this->backup_dir);
            }
        }
    }

    function copyFiles($files, $overwrite=null){
        $ftp = JClientHelper::getCredentials('ftp');

        // try to make writeable
        if ($overwrite || $this->getOverwrite()){
            foreach($files as $file){
                $type = array_key_exists('type', $file) ? $file['type'] : 'file';
                switch($type){
                    case 'file':
                        if (!$ftp['enabled'] && JFile::exists($file['dest']) && JPath::isOwner($file['dest'])){
                            JPath::setPermissions($file['dest']);
                        }
                        break;
                    case 'folder':
                        if (!$ftp['enabled'] && JFolder::exists($file['dest']) && JPath::isOwner($file['dest'])){
                            JPath::setPermissions($file['dest']);
                        }
                        break;
                }
            }
        }
        return parent::copyFiles($files, $overwrite);
    }
}
