<?php

/** 
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Validator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Controller Validator
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Validator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class LibBaseControllerValidatorAbstract extends KObject
{    
    /**
     * Validation error message
     * 
     * @return string
     */
    protected $_error_message;
        
    /**
     * Controller
     * 
     * @var KControllerAbstract
     */
    protected $_controller;
        
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {        
        parent::__construct($config);
        
        //Set the controller
        $this->_controller = $config->controller;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'controller' => null
        ));
    
        parent::_initialize($config);
    } 

    /**
     * Get the controller object
     *
     * @return  KControllerAbstract
     */
    public function getController()
    {
        return $this->_controller;
    }  
    
    /**
     * Set the error message
     * 
     * @return void
     */
    public function setMessage($string)
    {
        $this->_error_message = $string;
    }
    
    /**
     * Get error message
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->_error_message;
    }
}