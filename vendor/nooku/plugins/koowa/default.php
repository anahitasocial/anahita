<?php
/**
 * @version     $Id: default.php 4628M 2012-05-14 08:07:40Z (local) $
 * @package     Nooku_Plugins
 * @subpackage  Koowa
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Koowa plugin
 * 
 * Koowa plugins can handle a number of events that are dynamically generated. A plugin 
 * need to be wired to a specific event publisher in order for it to receive events from
 * that publisher. 
 * 
 * <code>
 * <?php
 * class PlgKoowaFoo extends PlgKoowaDefault
 * {
 *   protected function _initialize(KConfig $config)
 *   {
 *       $config->append(array(
 *           'event_publishers' => array('com://admin/foo.controller.bar')
 *       ));
 *        
 *       parent::_initialize($config);
 *   }	
 * }
 * </code>
 * 
 * 
 * The following is a list of available events. This list is not meant to be exclusive.
 * 
 * onBeforeController[Action]
 * onAfterController[Action]
 * where [Action] is Browse, Read, Edit, Add, Delete or any custom controller action
 * 
 * onBeforeDatabase[Action]
 * onAfterDatabase[Action]
 * where [Action] is Select, Insert, Update or Delete
 * 
 * You can create your own Koowa plugins very easily :
 * 
 * <code>
 * <?php
 * class PlgKoowaFoo extends PlgKoowaDefault
 * {
 * 		public function onBeforeControllerBrowse(KEvent $event)
 * 		{
 * 			//The publisher is a reference to the object that is triggering this event
 * 			$publisher = $event->getPublisher();
 * 
 * 			//The result is the actual result of the event, if this is an after event 
 * 			//the result will contain the result of the action.
 * 			$result = $event->result;
 * 
 * 			//The event object can also contain a number of custom properties
 *          print_r($event);
 * 		}	
 * }
 * </code>
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Plugins
 * @subpackage  Koowa
 */
abstract class PlgKoowaDefault extends KEventSubscriberDefault
{
	/**
	 * A JRegistry object holding the parameters for the plugin
	 *
	 * @var	A JRegistry object
	 */
	protected $_params	= null;

	/**
	 * Constructor
	 */
	public function __construct($dispatcher, $config = array())
	{
		if (!$config instanceof KConfig) {
	    	$config = new KConfig($config);
		}
	    
	    //Inject the identifier
		$config->service_identifier = KService::getIdentifier('plg:koowa.'.$config['name']);
		
		//Inject the service container
		$config->service_container = KService::getInstance();
		
		parent::__construct($config);
	
		//Set the plugin params
	    if(is_string($config->params)) {
            $config->params = $this->_parseParams($config->params);
        }
        
        $this->_params = $config->params;
        
        //Setup lazy wiring for publishers we are subscribing too
        foreach($config->event_publishers as $publisher) {
            KService::setConfig($publisher, array('event_subscribers' => array($this)));
        }
        
        if ( $dispatcher instanceof KEventDispatcher ) {
            $dispatcher->addEventSubscriber($this);
        }
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
	    $config->append(array(
        	'params'           => array(),
        	'event_publishers' => array()
        ));
	    
	    parent::_initialize($config);
	}
	
	/**
	 * Loads the plugin language file
	 *
	 * @param	string 	$extension 	The extension for which a language file should be loaded
	 * @param	string 	$basePath  	The basepath to use
	 * @return	boolean	True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = JPATH_BASE)
	{
		if(empty($extension)) {
		    $extension = 'plg_'.$this->getIdentifier()->package.'_'.$this->getIdentifier()->name;
		}

		return JFactory::getLanguage()->load( strtolower($extension), $basePath);
	}
	
	/**
	 * Method to extract key/value pairs out of a string
	 *
	 * @param   string  String containing the parameters
	 * @return  array   Key/Value pairs for the attributes
	 */
	protected function _parseParams( $string )
	{
	    $params = array();
	
	    if(!version_compare(JVERSION,'1.6.0','ge'))
	    {
	        $string = trim($string);
	
	        if(!empty($string))
	        {
	            foreach(explode("\n", $string) as $line)
	            {
	                $param = explode("=", $line, 2);
	                $params[$param[0]] = $param[1];
	            }
	        }
	    }
	    else $params = json_decode($string);
	     
	    $params = new KConfig($params);
	    return $params;
	}
}