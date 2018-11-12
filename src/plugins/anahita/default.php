<?php

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
*   protected function _initialize(AnConfig $config)
*   {
*       $config->append(array(
*           'event_publishers' => array('com://site/foo.controller.bar')
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
* class PlgAnahitaFoo extends PlgAnahitaDefault
* {
* 		public function onBeforeControllerBrowse(AnEvent $event)
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
* @author       Rastin Mehr <rastin@anahitapolis.com
* @package      anahita_Plugins
* @subpackage   Anahita
*/

abstract class PlgAnahitaDefault extends AnEventSubscriberDefault
{
    /**
     * Plugin Parameters
     *
     * @var AnConfig
     */
    protected $_params = null;

    /**
  	 * Constructor
  	 */
     public function __construct($dispatcher = null,  AnConfig $config)
     {
	    //Inject the identifier
		$config->service_identifier = AnService::getIdentifier('plg:anahita.'.$config->name);

		//Inject the service container
		$config->service_container = AnService::getInstance();

		parent::__construct($config);

        $this->_params = $config->meta;

        //Setup lazy wiring for publishers we are subscribing too
        foreach ($config->event_publishers as $publisher) {
            AnService::setConfig($publisher, array('event_subscribers' => array($this)));
        }

        if ($dispatcher instanceof AnEventDispatcher ) {
            $dispatcher->addEventSubscriber($this);
        }
  	}

  	/**
  	 * Initializes the options for the object
  	 *
  	 * Called from {@link __construct()} as a first step of object instantiation.
  	 *
  	 * @param   object  An optional AnConfig object with configuration options.
  	 * @return  void
  	 */
  	protected function _initialize(AnConfig $config)
  	{
  	    $config->append(array(
          	'params' => array(),
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
  	public function loadLanguage($extension = '', $basePath = ANPATH_BASE)
  	{
  		if (empty($extension)) {
  		    $extension = 'plg_'.$this->getIdentifier()->package.'_'.$this->getIdentifier()->name;
  		}

  		return AnService::get('anahita:language')->load( strtolower($extension), $basePath);
  	}
}
