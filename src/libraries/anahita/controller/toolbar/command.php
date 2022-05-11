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
 * @author      Rastin Mehr <rastin@anahita.io> 
 * @package     An_Controller
 * @subpackage 	Toolbar
 * @uses        AnInflector
 */
class AnControllerToolbarCommand extends AnConfig
{
 	/**
     * The command name
     *
     * @var string
     */
    protected $_name;

    /**
     * Constructor.
     *
     * @param	string 			The command name
     * @param   array|AnConfig 	An associative array of configuration settings or a AnConfig instance.
     */
    public function __construct( $name, $config = array() )
    {
        parent::__construct($config);

        $this->append(array(
            'id' => $name,
            'label' => ucfirst($name),
            'disabled' => false,
            'title' => '',
            'attribs' => array(
                'class' => array(),
            )
        ));

        //Set the command name
        $this->_name = $name;
    }

    /**
     * Get the command name
     *
     * @return string	The command name
     */
    public function getName()
    {
        return $this->_name;
    }
}
