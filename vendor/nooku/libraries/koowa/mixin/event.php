<?php
/**
 * @version     $Id: event.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Mixin
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Event Mixin
 * 
 * Class can be used as a mixin in classes that want to implement a an
 * event dispatcher and allow adding and removing listeners.
 *  
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Mixin
 * @uses        KEventDispatcher
 */
class KMixinEvent extends KMixinAbstract
{   
    /**
     * Event dispatcher object
     *
     * @var KEventDispatcher
     */
    protected $_event_dispatcher;
    
    /**
     * List of event subscribers
     *
     * Associative array of event subscribers, where key holds the subscriber identifier string
     * and the value is an identifier object.
     *
     * @var	array
     */
    protected $_event_subscribers = array();
    
    /**
     * Object constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        if(is_null($config->event_dispatcher)) {
			throw new KMixinException('event_dispatcher [KEventDispatcher] option is required');
		}
            
        //Set the event dispatcher
        $this->_event_dispatcher = $config->event_dispatcher;
        
        //Add the event listeners
        if(!empty($config->event_listeners))
        {
            foreach($config->event_listeners as $event => $listener) {
               $this->addEventListener($event, $listener);
            }
        }
        
        //Add the event handlers
        if(!empty($config->event_subscribers))
        {
            $subscribers = (array) KConfig::unbox($config->event_subscribers);
            
            foreach($subscribers as $key => $value) 
            {
                if(is_numeric($key)) {
                    $this->addEventSubscriber($value);
                } else {
                    $this->addEventSubscriber($key, $value);
                }
            }
        }
    }
    
    /**
     * Initializes the options for the object
     * 
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'event_dispatcher' => null,
            'event_subscribers'=> array(),
            'event_listeners'  => array(),
        ));
        
        parent::_initialize($config);
    }
    
	/**
     * Get the event dispatcher
     *
     * @return  KEventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->_event_dispatcher;
    }
    
    /**
     * Set the chain of command object
     *
     * @param   object 		An event dispatcher object
     * @return  KObject     The mixer object
     */
    public function setEventDispatcher(KEventDispatcher $dispatcher)
    {
        $this->_event_dispatcher = $dispatcher;
        return $this->getMixer();
    }
    
	/**
     * Add an event listener
     *
     * @param  string  The event name
     * @param  object  An object implementing the KObjectHandlable interface
     * @param  integer The event priority, usually between 1 (high priority) and 5 (lowest), 
     *                 default is 3. If no priority is set, the command priority will be used 
     *                 instead.
     * @return  KObject The mixer objects
     */
    public function addEventListener($event, KObjectHandlable $listener, $priority = KEvent::PRIORITY_NORMAL)
    {
        $this->_event_dispatcher->addEventListener($event, $listener, $priority);
        return $this->getMixer();
    }

    /**
     * Remove an event listener
     *
     * @param   string  The event name
     * @param   object  An object implementing the KObjectHandlable interface
     * @return  KObject  The mixer object
     */
    public function removeEventListener($event, KObjectHandlable $listener)
    {
        $this->_event_dispatcher->removeEventListener($event, $listener, $priority);
        return $this->getMixer();
    }
    
    /**
     * Add an event subscriber
     *
     * @param   mixed	An object that implements KObjectServiceable, KServiceIdentifier object 
	 * 					or valid identifier string
	 * @param  integer The event priority, usually between 1 (high priority) and 5 (lowest), 
     *                 default is 3. If no priority is set, the command priority will be used 
     *                 instead.
     * @return  KObject	The mixer object
     */
    public function addEventSubscriber($subscriber, $config = array(), $priority = null)
    {
        if (!($subscriber instanceof KEventSubscriberInterface)) {
            $subscriber = $this->getEventSubscriber($subscriber, $config);
        }
        
        $priority =  is_int($priority) ? $priority : $subscriber->getPriority(); 
        $this->_event_dispatcher->addEventSubscriber($subscriber, $priority);
    
        return $this;
    }
    
    /**
     * Remove an event listener
     *
     * @param   string  The event name
     * @param   object  An object implementing the KObjectHandlable interface
     * @return  KObject  The mixer object
     */
    public function removeEventDispatcher($subscriber)
    {
        if (!($subscriber instanceof KEventSubscriberInterface)) {
            $subscriber = $this->getEventSubscriber($subscriber, $config);
        }
        
        $this->_event_dispatcher->removeEventSubscriber($subscriber);
        return $this->getMixer();
    }
    
    /**
     * Get a event subscriber by identifier
     *
     * @return KEventSubsriberInterface
     */
    public function getEventSubscriber($subscriber, $config = array())
    {
        if(!($subscriber instanceof KServiceIdentifier))
        {
            //Create the complete identifier if a partial identifier was passed
            if(is_string($subscriber) && strpos($subscriber, '.') === false )
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('event', 'handler');
                $identifier->name = $subscriber;
            }
            else $identifier = $this->getIdentifier($subscriber);
        }
    
        if(!isset($this->_event_subscribers[(string) $identifier]))
        {
            $config['event_dispatcher'] = $this->getEventDispatcher();
             
            $subscriber = $this->getService($identifier, $config);
             
            //Check the event subscriber interface
            if(!($subscriber instanceof KEventSubscriberInterface)) {
                throw new KEventSubscriberException("Event Subscriber $identifier does not implement KEventSubscriberInterface");
            }
        }
        else $subscriber = $this->_event_subscribers[(string) $identifier];
         
        return $subscriber;
    }
}