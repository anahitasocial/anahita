<?php
/**
* @version		$Id: helper.php 17261 2010-05-25 15:06:51Z ian $
* @package		Joomla.Framework
* @subpackage	Plugin
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

/**
* Plugin helper class
*
* @static
* @package		Joomla.Framework
* @subpackage	Plugin
* @since		1.5
*/
class JPluginHelper
{
	/**
	 * Get the plugin data of a specific type if no specific plugin is specified
	 * otherwise only the specific plugin data is returned
	 *
	 * @access public
	 * @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
	 * @param string 	$plugin	The plugin name
	 * @return mixed 	An array of plugin data objects, or a plugin data object
	 */
	static public function &getPlugin($type, $name = null)
	{
		$results = array();

		$plugins = JPluginHelper::_load();

		foreach($plugins as $plugin){
			if (is_null($name)) {
				if($plugin->type == $type) {
					$results[] = $plugin;
				}
			} elseif($plugin->type === $type && $plugin->name === $name) {
				 $results = $plugin;
			}
		}

		return $results;
	}

	/**
	 * Checks if a plugin is enabled
	 *
	 * @access	public
	 * @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
	 * @param string 	$plugin	The plugin name
	 * @return	boolean
	 */
	static public function isEnabled( $type, $name = null )
	{
		$result = &JPluginHelper::getPlugin( $type, $name);
		return (!empty($result));
	}

	/**
	* Loads all the plugin files for a particular type if no specific plugin is specified
	* otherwise only the specific pugin is loaded.
	*
	* @access public
	* @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
	* @param string 	$plugin	The plugin name
	* @return boolean True if success
	*/
	static public function importPlugin($type, $name = null, $autocreate = true, $dispatcher = null)
	{
		$result = false;
		$plugins = JPluginHelper::_load();

		foreach ($plugins as $plugin) {
				if ($plugin->type === $type /* && ($name === null || $plugin->name === $name) */) {
					JPluginHelper::_import($plugin, $autocreate, $dispatcher);
					$result = true;
				}
		}

		return $result;
	}

	/**
	 * Loads the plugin file
	 *
	 * @access private
	 * @return boolean True if success
	 */
	static public function _import( &$plugin, $autocreate = true, $dispatcher = null )
	{
		static $paths;

		if (!$paths) {
			$paths = array();
		}

		$result	= false;
		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name  = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

		$path	= JPATH_PLUGINS.DS.$plugin->type.DS.$plugin->name.'.php';

		if (!isset( $paths[$path] ))
		{
			if (file_exists( $path ))
			{
				jimport('joomla.plugin.plugin');
				require_once( $path );
				$paths[$path] = true;

				if($autocreate)
				{
					$className = 'plg'.ucfirst($plugin->type).ucfirst($plugin->name);

					if(class_exists($className))
					{
						// load plugin parameters
						$plugin =& JPluginHelper::getPlugin($plugin->type, $plugin->name);

						// create the plugin
						$instance = new $className($dispatcher, (array) ($plugin));
					}
				}
			}
			else
			{
				$paths[$path] = false;
			}
		}
	}

	/**
	 * Loads the published plugins
	 *
	 * @access private
	 */
	static public function _load()
	{
		static $plugins;

		if (isset($plugins)) {
			return $plugins;
		}

		$db		=& JFactory::getDBO();
		$user	=& JFactory::getUser();

		//legacy field check
		$tableFields = $db->getTableFields('#__plugins');
		$metaField = isset($tableFields['#__plugins']['meta']) ? 'meta' : 'params';

		if (isset($user))
		{
			$aid = $user->get('aid', 0);

			$query = 'SELECT folder AS type, element AS name, '.$metaField.' AS meta'
				. ' FROM #__plugins'
				. ' WHERE published >= 1'
				. ' AND access <= ' . (int) $aid
				. ' ORDER BY ordering';
		}
		else
		{
			$query = 'SELECT folder AS type, element AS name, '.$metaField.' AS meta'
				. ' FROM #__plugins'
				. ' WHERE published >= 1'
				. ' ORDER BY ordering';
		}

		$db->setQuery( $query );

		if (!($plugins = $db->loadObjectList())) {
			JError::raiseWarning( 'SOME_ERROR_CODE', "Error loading Plugins: " . $db->getErrorMsg());
			return false;
		}

		foreach($plugins as $plugin){
			if($plugin->meta != ''){
					$meta = json_decode($plugin->meta, true);
					$plugin->meta = new KConfig($meta);
			}
		}

		return $plugins;
	}
}
