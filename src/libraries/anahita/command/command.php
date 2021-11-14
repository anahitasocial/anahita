<?php

/**
* Command handler
*
* The command handler will translate the command name into a function format and
* call it for the object class to handle it if the method exists.
*
* @author      Johan Janssens <johan@nooku.org>
* @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @package     AnCommand
* @link        https://www.Anahita.io
*/
class AnCommand extends AnObject implements AnCommandInterface
{
    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * The command priority
     *
     * @var integer
     */
    protected $_priority;

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

        $this->_priority = $config->priority;
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
            'priority' => AnCommand::PRIORITY_NORMAL,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler
     *
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.
     */
    public function execute($name, AnCommandContext $context)
    {
        $type = '';

        if ($context->caller) {
            $identifier = clone $context->caller->getIdentifier();

            if ($identifier->path) {
                $type = array_shift($identifier->path);
            } else {
                $type = $identifier->name;
            }
        }

        $parts = explode('.', $name);
        $method = !empty($type) ? '_'.$type.ucfirst(AnInflector::implode($parts)) : '_'.lcfirst(AnInflector::implode($parts));

        if (in_array($method, $this->getMethods())) {
            return $this->$method($context);
        }

        return true;
    }

    /**
     * Get the priority of the command
     *
     * @return  integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }
}
