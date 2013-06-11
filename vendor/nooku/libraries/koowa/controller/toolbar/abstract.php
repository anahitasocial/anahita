<?php
/**
* @version      $Id: abstract.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package		Koowa_Controller
* @subpackage 	Toolbar
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Abstract Controller Toolbar Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Controller
 * @subpackage 	Toolbar
 * @uses        KInflector
 */
abstract class KControllerToolbarAbstract extends KEventSubscriberAbstract implements KControllerToolbarInterface
{
    /**
     * The toolbar title
     *
     * @var     string
     */
    protected $_title = '';
    
    /**
     * The toolbar icon
     *
     * @var     string
     */
    protected $_icon = '';
     
    /**
     * Controller object
     *
     * @var     array
     */
    protected $_controller = null;
    
    /** 
     * The commands
     *
     * @var array
     */
    protected $_commands = array();

    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KConfig();
        
        parent::__construct($config);
        
        if(is_null($config->controller)) {
			throw new KMixinException('controller [KController] option is required');
		}
        
        // Set the controller
        $this->_controller = $config->controller;

        // Set the title
        $this->setTitle($config->title);
        
        // Set the icon
        $this->setIcon($config->icon);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'title'         => KInflector::humanize(KInflector::pluralize($this->getName())),
            'icon'          => $this->getName(),
            'controller'    => null,
        ));
        
        parent::_initialize($config);
    }
    
	/**
     * Get the controller object
     * 
     * @return  KController
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Get the toolbar's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getIdentifier()->name;
    }
    
    /**
     * Set the toolbar's title
     *
     * @param   string  Title
     * @return  KControllerToolbarAbstract
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }
    
 	/**
     * Get the toolbar's title
     *
     * @return   string  Title
     */
    public function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * Set the toolbar's icon
     *
     * @param   string  Icon
     * @return  KControllerToolbarAbstract
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }
    
	/**
     * Get the toolbar's icon
     *
     * @return   string  Icon
     */
    public function getIcon()
    {
        return $this->_icon;
    }
    
    /**
     * Add a separator
     *
     * @return  KControllerToolbarAbstract
     */
    public function addSeparator()
    {
        $this->_commands[] = new KControllerToolbarCommand('separator');
        return $this;
    }
     
    /**
     * Add a command
     *
     * @param   string	The command name
     * @param	mixed	Parameters to be passed to the command
     * @return  KControllerToolbarAbstract
     */
    public function addCommand($command, $config = array()) 
    {
        if (!($command instanceof  KControllerToolbarCommand)) { 
            $command = $this->getCommand($command, $config);
        }
		               
        $this->_commands[$command->getName()] = $command;
        return $this;
    }
    
	/** 
     * Get a command by name
     * 
     * @param string The command name
     * @param array An optional associative array of configuration settings
     * @return mixed KControllerToolbarCommand if found, false otherwise.  
     */ 
    public function getCommand($name, $config = array()) 
    { 
        if (!isset($this->_commands[$name])) 
        { 
            //Create the config object 
            $command = new KControllerToolbarCommand($name, $config);
        
            //Find the command function to call
            if(method_exists($this, '_command'.ucfirst($name))) 
            {
                $function =  '_command'.ucfirst($name);
                $this->$function($command);
            }     
            else 
            {
                //Don't set an action for GET commands
                if(!isset($command->attribs->href)) 
                {
                    $command->append(array(
         				'attribs'    => array(
               				'data-action'  => $command->getName()
                        )
                    ));
                }
            }
        } 
        else $command = $this->_commands[$name]; 

        return $command;
    } 
    
 	/**
     * Get the list of commands
     *
     * @return  array
     */
    public function getCommands()
    {
        return $this->_commands;   
    }
 
    /**
     * Reset the commands array
     *
     * @return  KConttrollerToolbarAbstract
     */
    public function reset()
    {
        $this->_commands = array();
        return $this;
    }
   
 	/**
     * Add a command by it's name
	 *
     * @param   string  Method name
     * @param   array   Array containing all the arguments for the original call
     * @see addCommand()
     */
    public function __call($method, $args)
    {  
		$parts = KInflector::explode($method);

		if($parts[0] == 'add' && isset($parts[1]))
		{
		    $config = isset($args[0]) ? $args[0] : array();	    
		    $this->addCommand(strtolower($parts[1]), $config);
			return $this;
		}
        
        return parent::__call($method, $args);
    }
}