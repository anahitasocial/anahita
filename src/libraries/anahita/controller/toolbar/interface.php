<?php
/**
* @package		An_Controller
* @subpackage 	Toolbar
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Abstract Controller Toolbar Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @author      Rastin Mehr <rastin@anahitapolis.com> 
 * @package     An_Controller
 * @subpackage 	Toolbar
 * @uses        AnInflector
 */
interface AnControllerToolbarInterface
{
	/**
     * Get the controller object
     *
     * @return  AnController
     */
    public function getController();

    /**
     * Get the toolbar's name
     *
     * @return string
     */
    public function getName();

    /**
     * Set the toolbar's title
     *
     * @param   string  Title
     * @return  AnControllerToolbarInterface
     */
    public function setTitle($title);

 	/**
     * Get the toolbar's title
     *
     * @return   string  Title
     */
    public function getTitle();

    /**
     * Add a separator
     *
     * @return  AnControllerToolbarInterface
     */
    public function addSeparator();

    /**
     * Add a command
     *
     * @param   string	The command name
     * @param	mixed	Parameters to be passed to the command
     * @return  AnControllerToolbarInterface
     */
    public function addCommand($name, $config = array());

 	/**
     * Get the list of commands
     *
     * @return  array
     */
    public function getCommands();

    /**
     * Reset the commands array
     *
     * @return  KConttrollerToolbarInterface
     */
    public function reset();
}
