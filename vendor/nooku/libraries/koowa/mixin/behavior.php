<?php
/**
 * @version     $Id: behavior.php 4628M 2012-05-11 07:06:16Z (local) $
 * @package     Koowa_Mixin
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Behavior Mixin Class
 *  
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Mixin
 */
class KMixinBehavior extends KMixinAbstract
{  
	/**
	 * List of behaviors
	 * 
	 * Associative array of behaviors, where key holds the behavior identifier string
	 * and the value is an identifier object.
	 * 
	 * @var	array
	 */
	protected $_behaviors = array();
	
	/**
	 * Auto mixin behaviors
	 * 
	 * @var boolean
	 */
	protected $_auto_mixin;
	
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		//Set the auto mixin state
		$this->_auto_mixin = $config->auto_mixin;
		
		if ( $config->mixer instanceof KObject )
		    $config->mixer->mixin($this);
		
	    //Add the behaviors
        if(!empty($config->behaviors)) 
        {
            $behaviors = (array) KConfig::unbox($config->behaviors);
            
            foreach($behaviors as $key => $value) 
            {
                if(is_numeric($key)) {
                    $this->addBehavior($value);
                } else {
                    $this->addBehavior($key, $value);
                }
            }
        } 
	}
	
	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	parent::_initialize($config);
    	
        $config->append(array(
    		'behaviors'  => array(),
            'auto_mixin' => true
        ));
    }
    
	/**
     * Check if a behavior exists
     *
     * @param 	string	The name of the behavior
     * @return  boolean	TRUE if the behavior exists, FALSE otherwise
     */
	public function hasBehavior($behavior)
	{ 
	    return isset($this->_behaviors[$behavior]); 
	}
	
	/**
     * Add one or more behaviors to the controller
     *
     * @param   mixed	An object that implements KObjectServiceable, KServiceIdentifier object 
	 * 					or valid identifier string
     * @param	array An optional associative array of configuration settings
     * @return  KObject	The mixer object
     */
    public function addBehavior($behavior, $config = array())
    {  
        if (!($behavior instanceof KBehaviorInterface)) { 
           $behavior = $this->_mixer->getBehavior($behavior, $config);
        }
                
        //Add the behaviors
        $this->_behaviors[$behavior->getIdentifier()->name] = $behavior;
            
        //Enqueue the behavior
        $this->getCommandChain()->enqueue($behavior);
        
        //Mixin the behavior
        if($this->_auto_mixin) {
            $this->mixin($behavior);
        }
        
        return $this->_mixer;
    }
   
	/**
     * Get a behavior by identifier
     *
     * @param   mixed	An object that implements KObjectServiceable, KServiceIdentifier object 
	 * 					or valid identifier string
     * @param	array An optional associative array of configuration settings
     * @return KControllerBehaviorAbstract
     */
    public function getBehavior($behavior, $config = array())
    {
       $identifier = $behavior;        
       if(!($behavior instanceof KServiceIdentifier))
       {
            //Create the complete identifier if a partial identifier was passed
           if(is_string($behavior) && strpos($behavior, '.') === false )
           {
               $identifier = clone $this->getIdentifier();
               $identifier->path  = array($identifier->path[0], 'behavior');
               $identifier->name  = $behavior;
           }
           else $identifier = $this->getIdentifier($behavior);
       }
           
       if(!isset($this->_behaviors[$identifier->name])) 
       {
           $config['mixer'] = $this->getMixer();
           
           $behavior = $this->getService($identifier, $config);
           
           //Check the behavior interface
		   if(!($behavior instanceof KBehaviorInterface)) {
			   throw new KBehaviorException("Behavior $identifier does not implement KBehaviorInterface");
		   }
       } 
       else $behavior = $this->_behaviors[$identifier->name];
       
       return $behavior;
    }
    
    /**
     * Gets the behaviors of the table
     *
     * @return array    An asscociate array of table behaviors, keys are the behavior names
     */
    public function getBehaviors()
    {
        return $this->_behaviors;
    }
}