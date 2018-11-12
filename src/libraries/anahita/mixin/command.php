<?php

/**
 * Command Mixin
 *
 * Class can be used as a mixin in classes that want to implement a chain
 * of responsability or chain of command pattern.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://www.GetAnahita.com
 * @package     AnMixin
 * @uses        KObject
 * @uses        AnCommandChain
 * @uses        AnCommandInterface
 * @uses        AnCommandEvent
 */
class AnMixinCommand extends AnMixinAbstract
{
    /**
     * Chain of command object
     *
     * @var AnCommandChain
     */
    protected $_command_chain;
    
    /**
     * Object constructor
     *
     * @param   object  An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        
        if (is_null($config->command_chain)) {
            throw new AnMixinException('command_chain [AnCommandChain] option is required');
        }
            
        //Create a command chain object
        $this->_command_chain = $config->command_chain;
        
        //Set the mixer in the config
        $config->mixer = $this->_mixer;
        
        //Mixin the callback mixer if callbacks have been enabled
        if ($config->enable_callbacks) {
            $this->_mixer->mixin(new AnMixinCallback($config));
        }
        
        //Enqueue the event command with a lowest priority to make sure it runs last
        if ($config->dispatch_events) {
            $this->_mixer->mixin(new AnMixinEvent($config));
            
            //@TODO : Add AnCommandChain::getCommand()
            $event = $this->_command_chain->getService('anahita:command.event', array(
                'event_dispatcher' => $config->event_dispatcher
            ));
            
            $this->_command_chain->enqueue($event, $config->event_priority);
        }
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional AnConfig object with configuration options
     * @return  void
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'command_chain'     => null,
            'event_dispatcher'  => null,
            'dispatch_events'   => true,
            'event_priority'    => AnCommand::PRIORITY_LOWEST,
            'enable_callbacks'  => false,
            'callback_priority' => AnCommand::PRIORITY_HIGH,
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Get the command chain context
     *
     * This functions inserts a 'caller' variable in the context which contains
     * the mixer object.
     *
     * @return  AnCommandContext
     */
    public function getCommandContext()
    {
        $context = $this->_command_chain->getContext();
        $context->caller = $this->_mixer;
        
        return $context;
    }
    
    /**
     * Get the chain of command object
     *
     * @return  AnCommandChain
     */
    public function getCommandChain()
    {
        return $this->_command_chain;
    }
    
    /**
     * Set the chain of command object
     *
     * @param   object 	A command chain object
     * @return  KObject The mixer object
     */
    public function setCommandChain(AnCommandChain $chain)
    {
        $this->_command_chain = $chain;
        return $this->_mixer;
    }
    
    /**
     * Preform a deep clone of the object.
     *
     * @retun void
     */
    public function __clone()
    {
        $this->_command_chain = clone $this->_command_chain;
    }
}
