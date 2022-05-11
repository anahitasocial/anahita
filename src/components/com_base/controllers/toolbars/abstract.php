<?php

/**
 * Abstract Toolbar.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
abstract class ComBaseControllerToolbarAbstract extends AnControllerToolbarAbstract
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        //commands is template objects container
        $this->_commands = new LibBaseTemplateObjectContainer();
    }

    /**
     * Resets the commands container.
     */
    public function reset()
    {
        $this->_commands->reset();
    }

    /**
     * Add a command.
     *
     * @param string $command Comamd name
     * @param array  $config  Parameters to be passed to the command
     *
     * @return AnControllerToolbarAbstract
     */
    public function addCommand($command, $config = array())
    {
        if (!($command instanceof LibBaseTemplateObjectInterface)) {
            $command = $this->getCommand($command, $config);
        }

        $this->_commands[$command->getName()] = $command;

        return $this;
    }

    /**
     * Get a command by name.
     *
     * @param string The command name
     * @param array  An optional associative array of configuration settings
     *
     * @see LibBaseTemplateObject
     *
     * @return LibBaseTemplateObject Return a template object
     */
    public function getCommand($name, $config = array())
    {
        if (!isset($this->_commands[$name])) {
            $command = ComBaseControllerToolbarCommand::getInstance($name, $config);
        } else {
            $command = $this->_commands[$name];
        }

        return $command;
    }

    /**
     * Clones the toolbar.
     */
    public function __clone()
    {
        $this->_commands = clone $this->_commands;
    }
}
