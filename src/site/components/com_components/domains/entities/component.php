<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Component object
 *
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComponentsDomainEntityComponent extends LibComponentsDomainEntityComponent implements KEventSubscriberInterface
{
	/**
	 * Subscriptions
	 * 
	 * @var array
	 */
	private $__subscriptions = array();
	
	/**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		JFactory::getLanguage()->load('com_'.$this->getIdentifier()->package);
		
		$config->append(array(
				
		));		
		
		parent::_initialize($config);
	}
		
	/**
	 * Registers event dispatcher
	 *
	 * @param KEventDispatcher $dispatcher Event dispatche
	 *
	 * @return void
	 */
	public function registerEventDispatcher(KEventDispatcher $dispatcher)
	{						
		$dispatcher->addEventSubscriber($this);
	}	
		
	/**
	 * Get the priority of the handler
	 *
	 * @return	integer The event priority
	 */
	public function getPriority()
	{		
		return $this->ordering;
	}
	
	/**
	 * Get a list of subscribed events
	 *
	 * Event handlers always start with 'on' and need to be public methods
	 *
	 * @return array An array of public methods
	 */
	public function getSubscriptions()
	{
		if(!$this->__subscriptions)
		{
			$subscriptions  = array();
	
			//Get all the public methods
            //$reflection = new ReflectionClass($this);
            //foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
            foreach($this->getMethods() as $method)
			{
				if(substr($method, 0, 2) == 'on') {
					$subscriptions[] = $method;
				}
			}
	
			$this->__subscriptions = $subscriptions;
		}
	
		return $this->__subscriptions;
	}
		
	/**
	 * Return an array of identifiers within the component
	 *
	 * @param string $class The class from which the entities are inherting
	 *
	 * @return array()
	 */	
	public function getEntityRepositories($class)
	{
		$identifiers = $this->getEntityIdentifiers($class);
		foreach($identifiers as $i => $identifier) {
			$identifiers[$i] = AnDomain::getRepository($identifier);
		}
		return $identifiers;
	}
		
	/**
	 * Return an array of identifiers within the component
	 * 
	 * @param string $class The class from which the entities are inherting 
	 * 
	 * @return array()
	 */
	public function getEntityIdentifiers($class)
	{
		$registry = $this->getService('application.registry', array('key'=>$this->getIdentifier()));		
		if ( !$registry->offsetExists($class.'-identifiers') ) 
		{
			$path     = JPATH_ROOT.DS.'components'.DS.'com_'.$this->getIdentifier()->package.DS.'domains'.DS.'entities';
			$identifiers = array();
			if ( file_exists($path) )
			{
				$files = JFolder::files($path);
				foreach($files as $file) {
					$name       = explode('.', basename($file));
					$name       = $name[0];
					$identifier = clone $this->getIdentifier();
					$identifier->path = array('domain','entity');
					$identifier->name = $name;
					try {
						if ( is($identifier->classname, $class) ) {
							$identifiers[] = $identifier;
						}
					}
					catch(Exception $e) {}
				}
			}
			$registry->offsetSet($class.'-identifiers', $identifiers);
		}		
		$identifiers = $registry->offsetGet($class.'-identifiers');
		return $identifiers;
	}
}