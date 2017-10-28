<?php

/**
 * Abstract Controller Validator.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.GetAnahita.com
 */
abstract class LibBaseControllerValidatorAbstract extends KObject
{
    /**
     * Validation error message.
     *
     * @return string
     */
    protected $_error_message;

    /**
     * Controller.
     *
     * @var AnControllerAbstract
     */
    protected $_controller;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Set the controller
        $this->_controller = $config->controller;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'controller' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the controller object.
     *
     * @return AnControllerAbstract
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Set the error message.
     */
    public function setMessage($string)
    {
        $this->_error_message = $string;
    }

    /**
     * Get error message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_error_message;
    }
}
