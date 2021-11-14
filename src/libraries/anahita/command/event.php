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
 * @link        https://www.Anahita.io
 */
class AnCommandEvent extends AnCommand
{
    /**
     * The event dispatcher object
     *
     * @var AnEventDispatcher
     */
    protected $_dispatcher;
    
    /**
     * Constructor.
     *
     * @param   object  An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config = null)
    {
        //If no config is passed create it
        if (! isset($config)) {
            $config = new AnConfig();
        }
        
        parent::__construct($config);
        
        if (is_null($config->event_dispatcher)) {
            throw new AnMixinException('event_dispatcher [AnEventDispatcher] option is required');
        }
        
        $this->_event_dispatcher = $config->event_dispatcher;
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional AnConfig object with configuration options
     * @return void
     */
    protected function _initialize(AnConfig $config)
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
    public function execute($name, AnCommandContext $context)
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
        
        $event = new AnEvent(clone($context));
        $event->setPublisher($context->caller);
        
        $this->_event_dispatcher->dispatchEvent($name, $event);
        
        return true;
    }
}
