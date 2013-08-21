<?php
/**
* @version		$Id: helper.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Application
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
 * Component helper class
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	Application
 * @since		1.5
 */
class JComponentHelper
{
	/**
	 * Get the component info
	 *
	 * @access	public
	 * @param	string $name 	The component name
	 * @param 	boolean	$string	If set and a component does not exist, the enabled attribue will be set to false
	 * @return	object A JComponent object
	 */
	static function &getComponent( $name, $strict = false )
	{
		$result = null;
		$components = JComponentHelper::_load();

		if (isset( $components[$name] ))
		{
			$result = &$components[$name];
		}
		else
		{
			$result				= new stdClass();
			$result->enabled	= $strict ? false : true;
			$result->params		= null;
		}

		return $result;
	}

	/**
	 * Checks if the component is enabled
	 *
	 * @access	public
	 * @param	string	$component The component name
	 * @param 	boolean	$string	If set and a component does not exist, false will be returned
	 * @return	boolean
	 */
	static function isEnabled( $component, $strict = false )
	{
		global $mainframe;

		$result = &JComponentHelper::getComponent( $component, $strict );
		return ($result->enabled | $mainframe->isAdmin());
	}

	/**
	 * Gets the parameter object for the component
	 *
	 * @access public
	 * @param string $name The component name
	 * @return object A JParameter object
	 */
	static function &getParams( $name )
	{
		static $instances;
		if (!isset( $instances[$name] ))
		{
			$component = &JComponentHelper::getComponent( $name );
			$instances[$name] = new JParameter($component->params);
		}
		return $instances[$name];
	}

	static function renderComponent($name, $params = array())
	{
		global $mainframe, $option;
		
		// Define component path
		define( 'JPATH_COMPONENT',					JPATH_BASE.DS.'components'.DS.$name);
		define( 'JPATH_COMPONENT_SITE',				JPATH_SITE.DS.'components'.DS.$name);
		define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.$name);

		if ( !file_exists(JPATH_COMPONENT) ) {
		    JError::raiseError( 404, JText::_( 'Component Not Found' ) );
		}

		$file = substr( $name, 4 );
		
		// get component path
		if ( $mainframe->isAdmin() && file_exists(JPATH_COMPONENT.DS.'admin.'.$file.'.php') ) {
			$path = JPATH_COMPONENT.DS.'admin.'.$file.'.php';
		} else {
			$path = JPATH_COMPONENT.DS.$file.'.php';
		}
		
        $identifier = KService::getIdentifier("com:$file.aliases");
        $identifier->application = $mainframe->isAdmin()  ? 'admin' : 'site';        
        $lang =& JFactory::getLanguage();
        $lang->load($name);
        KLoader::getInstance()->loadIdentifier($identifier);
        
        //new way of doing it
        if ( !file_exists($path) ) 
        {
            $identifier->name = 'dispatcher';
            register_default(array('identifier'=>$identifier,'default'=>'ComBaseDispatcherDefault'));
            $dispatcher = ComBaseDispatcher::getInstance();
            KService::setAlias('component.dispatcher', $dispatcher->getIdentifier());
            KService::set('component.dispatcher', $dispatcher);
            return $dispatcher->dispatch();
        }
        else 
        {
            $contents = self::_renderComponent($path);
                      
            // Build the component toolbar
            jimport( 'joomla.application.helper' );
            if (($path = JApplicationHelper::getPath( 'toolbar' )) && $mainframe->isAdmin())
            {
                // Get the task again, in case it has changed
                $task = JRequest::getString( 'task' );
            
                // Make the toolbar
                include_once( $path );
            }
                        
            return $contents;            
        }
	}
	
	/**
	 * Render the contents of a component
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static protected function _renderComponent($path)
	{
	    //getting the task for old components
	    $task = JRequest::getString( 'task' );
	    ob_start();
	    require_once $path;
	    $contents = ob_get_contents();
	    ob_end_clean();
	    return $contents;	    
	}

	/**
	 * Load components
	 *
	 * @access	private
	 * @return	array
	 */
	static function _load()
	{
		static $components;

		if (isset($components)) {
			return $components;
		}

		$db = &JFactory::getDBO();

		$query = 'SELECT *' .
				' FROM #__components' .
				' WHERE parent = 0';
		$db->setQuery( $query );

		if (!($components = $db->loadObjectList( 'option' ))) {
			JError::raiseWarning( 'SOME_ERROR_CODE', "Error loading Components: " . $db->getErrorMsg());
			return false;
		}

		return $components;

	}
}
