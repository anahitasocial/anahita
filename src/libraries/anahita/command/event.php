<?php
 
 /**
 * Event Command
 * 
 * The event commend will translate the command name to a onCommandName format 
 * and let the event dispatcher dispatch to any registered event handlers.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @package     AnCommand
 * @link        https://www.GetAnahita.com
 */
class AnCommandEvent extends AnCommand
{
    /**
     * The event dispatcher object
     *
     * @var KEventDispatcher
     */
    protected $_dispatcher;
    
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config = null) 
    { 
        //If no config is passed create it
        if (! isset($config)) $config = new KConfig();
        
        parent::__construct($config);
        
        if (is_null($config->event_dispatcher)) {
			throw new AnMixinException('event_dispatcher [KEventDispatcher] option is required');
		}
        
        $this->_event_dispatcher = $config->event_dispatcher;
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'event_dispatcher'   => null
        ));

        parent::_initialize($config);
    }
    
    /**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Always returns true
     */
    public function execute( $name, AnCommandContext $context) 
    {
        $type = '';
        
        if ($context->caller) {   
            $identifier = $context->caller->getIdentifier();
            
            if ($identifier->path) {
                $type = AnInflector::implode($identifier->path);                
            } else {
                $type = $identifier->name;
            }
        }
        
        $parts = explode('.', $name);   
        $name = 'on'.ucfirst(array_shift($parts)).ucfirst($type).AnInflector::implode($parts);
        
        $event = new KEvent(clone($context));
        $event->setPublisher($context->caller);
        
        $this->_event_dispatcher->dispatchEvent($name, $event);
        
        return true;
    }
}