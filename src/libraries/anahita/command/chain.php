<?php

/**
* Command Chain
*
* The command queue implements a double linked list. The command handle is used
* as the key. Each command can have a priority, default priority is 3 The queue
* is ordered by priority, commands with a higher priority are called first.
*
* @author      Johan Janssens <johan@nooku.org>
* @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @package     AnCommand
* @link        https://www.GetAnahita.com
*/
class AnCommandChain extends KObjectQueue
{
    /**
     * Enabled status of the chain
     *
     * @var boolean
     */
    protected $_enabled = true;
    
    /**
     * The chain's break condition
     *
     * @see run()
     * @var boolean
     */
    protected $_break_condition = false;
    
    /**
     * The command context object
     *
     * @var AnCommandContext
     */
    protected $_context = null;

    /**
     * The chain stack
     *
     * @var     KObjectStack
     */
    protected $_stack;

    /**
     * Constructor
     *
     * @param AnConfig|null $config  An optional AnConfig object with configuration options
     * @return \AnCommandChain
     */
    public function __construct(AnConfig $config = null)
    {
        //If no config is passed create it
        if (! isset($config)) {
            $config = new AnConfig();
        }
        
        parent::__construct($config);
        
        $this->_break_condition = (boolean) $config->break_condition;
        $this->_enabled = (boolean) $config->enabled;
        $this->_context = $config->context;
        $this->_stack = $config->stack;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   AnConfig $object An optional AnConfig object with configuration options
     * @return  void
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'stack' => $this->getService('koowa:object.stack'),
            'context' => new AnCommandContext(),
            'enabled' => true,
            'break_condition' => false,
        ));
        
        parent::_initialize($config);
    }
    
    /**
     * Attach a command to the chain
     *
     * The priority parameter can be used to override the command priority while enqueueing the command.
     *
     * @param AnCommandInterface $command
     * @param integer $priority The command priority, usually between 1 (high priority) and 5 (lowest),
     *                default is 3. If no priority is set, the command priority will be used instead.
     *
     * @return AnCommandChain
     * @throws InvalidArgumentException if the object doesn't implement AnCommandInterface
     */
    public function enqueue(KObjectHandlable $command, $priority = null)
    {
        if (! $command instanceof AnCommandInterface) {
            throw new InvalidArgumentException('Command needs to implement AnCommandInterface');
        }

        $priority = is_int($priority) ? $priority : $command->getPriority();
        return parent::enqueue($command, $priority);
    }

    /**
     * Removes a command from the queue
     *
     * @param AnCommandInterface $command
     * @return boolean	TRUE on success FALSE on failure
     * @throws InvalidArgumentException if the object implement AnCommandInterface
     */
    public function dequeue(KObjectHandlable $command)
    {
        if (! $command instanceof AnCommandInterface) {
            throw new InvalidArgumentException('Command needs to implement AnCommandInterface');
        }

        return parent::dequeue($command);
    }

    /**
     * Check if the queue does contain a given object
     *
     * @param AnCommandInterface $object
     * @return bool
     * @throws InvalidArgumentException if the object implement AnCommandInterface
     */
    public function contains(KObjectHandlable $command)
    {
        if (! $command instanceof AnCommandInterface) {
            throw new InvalidArgumentException('Command needs to implement AnCommandInterface');
        }

        return parent::contains($command);
    }

    /**
     * Run the commands in the chain
     *
     * If a command returns the 'break condition' the executing is halted.
     *
     * @param string $name
     * @param AnCommandContext $context
     * @return void|boolean If the chain breaks, returns the break condition. Default returns void.
     */
    public function run($name, AnCommandContext $context)
    {
        if ($this->_enabled) {
            $this->getStack()->push(clone $this);

            foreach ($this->getStack()->top() as $command) {
                if ($command->execute($name, $context) === $this->_break_condition) {
                    $this->getStack()->pop();
                    return $this->_break_condition;
                }
            }

            $this->getStack()->pop();
        }
    }
    
    /**
     * Enable the chain
     *
     * @return  void
     */
    public function enable()
    {
        $this->_enabled = true;
        return $this;
    }
    
    /**
     * Disable the chain
     *
     * If the chain is disabled running the chain will always return TRUE
     *
     * @return  void
     */
    public function disable()
    {
        $this->_enabled = false;
        return $this;
    }
    
    /**
     * Set the priority of a command
     *
     * @param AnCommandInterface $command
     * @param integer           $priority
     * @return \AnCommandChain
     * @throws InvalidArgumentException if the object doesn't implement AnCommandInterface
     */
    public function setPriority(KObjectHandlable $command, $priority)
    {
        if (! $command instanceof AnCommandInterface) {
            throw new InvalidArgumentException('Command needs to implement AnCommandInterface');
        }

        return parent::setPriority($cmd, $priority);
    }
    
    /**
     * Get the priority of a command
     *
     * @param  AnCommandInterface $object
     * @return integer The command priority
     * @throws InvalidArgumentException if the object doesn't implement AnCommandInterface
     */
    public function getPriority(KObjectHandlable $command)
    {
        if (! $command instanceof AnCommandInterface) {
            throw new InvalidArgumentException('Command needs to implement AnCommandInterface');
        }

        return parent::getPriority($command);
    }
    
    /**
     * Factory method for a command context.
     *
     * @return  AnCommandContext
     */
    public function getContext()
    {
        return clone $this->_context;
    }

    /**
     * Get the chain object stack
     *
     * @return  KObjectStack
     */
    public function getStack()
    {
        return $this->_stack;
    }
}
