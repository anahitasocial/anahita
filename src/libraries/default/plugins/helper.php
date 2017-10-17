<?php
/**
 *
 *
 * @category  Anahita
 * @package   libraries
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008-2016 rmd Studio Inc.
 * @license    GNU GPLv3
 *
 * @link       http://www.GetAnahita.com
 */

class LibPluginsHelper extends KObject implements KServiceInstantiatable
{
    /**
    *   array of plugins
    */
    protected $_plugins = array();

    /**
    *   array of plugin paths
    */
    protected $_paths = array();

    /**
     * Force creation of a singleton.
     *
     * @param   object  An optional KConfig object with configuration options
     * @param   object  A KServiceInterface object
     *
     * @return KDispatcherDefault
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier)) {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
  	 * Get the plugin data of a specific type if no specific plugin is specified
  	 * otherwise only the specific plugin data is returned
  	 *
  	 * @access public
  	 * @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
  	 * @param string 	$plugin	The plugin element
  	 * @return mixed 	An array of plugin data objects, or a plugin data object
  	 */
  	public function &getPlugin($type, $element = null)
  	{
		$results = array();
		$this->_load();

		foreach($this->_plugins as $plugin) {
			if (is_null($element)) {
				if($plugin->type == $type) {
					$results[] = $plugin;
				}
			} elseif($plugin->type == $type && $plugin->element == $element) {
				 $results = $plugin;
			}
		}

		return $results;
  	}

    /**
  	* Loads all the plugin files for a particular type if no specific plugin is specified
  	* otherwise only the specific pugin is loaded.
  	*
  	* @access public
  	* @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
  	* @param string 	$plugin	The plugin element
  	* @return boolean True if success
  	*/
    public function import($type, $element = null, $autocreate = true, $dispatcher = null)
    {
        $result = false;
        $this->_load();

        foreach ($this->_plugins as $plugin) {
            if ($plugin->type === $type && (is_null($element) || $plugin->element === $element)) {
                $this->_import($plugin, $autocreate, $dispatcher);
                $result = true;
            }
        }

        return $result;
    }

    /**
  	 * Checks if a plugin is enabled
  	 *
  	 * @access	public
  	 * @param string 	$type 	The plugin type, relates to the sub-directory in the plugins directory
  	 * @param string 	$plugin	The plugin name
  	 * @return	boolean
  	 */
  	public function isEnabled($type, $element = null)
  	{
    	return (boolean) $this->getPlugin($type, $element);
  	}

    /**
  	 * Loads the plugin file
  	 *
  	 * @access protected
  	 * @return boolean True if success
  	 */
    protected function _import($plugin, $autocreate = true, $dispatcher = null)
    {
        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
        $element  = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->element);
        $path	= ANPATH_PLUGINS.DS.$type.DS.$element.'.php';

        if (isset($this->_paths[$path]) && $this->_paths[$path] === true) {
           return true;
        }

        if(! file_exists($path)) {
           $this->_paths[$path] = false;
           return false;
        }

        require_once($path);
        $this->_paths[$path] = true;

        if ($autocreate) {
            $className = 'plg'.ucfirst($type).ucfirst($element);
            if (class_exists($className)) {
                $config = array(
                  'id' => $plugin->id,
                  'name' => $plugin->name,
                  'type' => $plugin->type,
                  'element' => $plugin->element,
                  'meta' => $plugin->meta
                );
                $config = new KConfig($config);
                $instance = new $className($dispatcher, $config);
            }
        }

        return true;
    }

    /**
  	 * Loads the published plugins
  	 *
  	 * @access private
  	 */
    protected function _load()
    {
        if (count($this->_plugins) == 0) {
            $this->_plugins = KService::get('repos:settings.plugin')
                              ->getQuery()
                              ->where('enabled', '=', 1)
                              ->toEntityset();
        }

        return $this->_plugins;
    }
}
