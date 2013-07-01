<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Parentable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorParentable extends KControllerBehaviorAbstract
{
    /**
     * Identifiable key. If this key exists in the request then this behavior
     * will fetch the actor entity using this key
     * 
     * @return string
     */
    protected $_identifiable_key;
    
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
        
        //nullify the parent
        $this->setParent(null);
        
        //set the identifiable key. By default its set to pid
        $this->_identifiable_key = $config->identifiable_key;
        
        //insert the state
        $this->getState()->insert($this->_identifiable_key);
    }
        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'identifiable_key' => 'pid',
            'priority'         => KCommand::PRIORITY_HIGHEST
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
    public function execute($name, KCommandContext $context) 
    {
		$parts = explode('.', $name);
		
		if ( $parts[0] == 'before' ) 
		{
            $value = pick($this->{$this->getIdentifiableKey()}, $context->data->{$this->getIdentifiableKey()});
						
			if ( $value) 
            {
                $parent = $this->getParentRepository()->fetch($value);
                
                $this->setParent($parent);
                
                //set the entity owner as the context actor of the controller
                if ( $parent && $this->getRepository()->isOwnable() && $this->isOwnable() ) {
                    $this->setActor($parent->owner);
                }
                
                $context->data['parent'] = $parent;
			}
		}
    } 
    
    /**
     * Set the parent
     * 
     * @param AnDomainEntityDefault $parent Set the parent entity
     * 
     * @return ComBaseControllerBehaviorParentable
     */
    public function setParent($parent)
    {
       $this->_mixer->parent = $parent;
       return $this;
    }
    
    
    /**
     * Return the parent entity
     * 
     * @return AnDomainEntityDefault
     */
    public function getParent()
    {
        return $this->_mixer->parent;
    }
    
    /**
     * Return the parent repository
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getParentRepository()
    {
        $parent = $this->getRepository()->getDescription()->getProperty('parent');
        $parent 	= $this->getIdentifier($parent->getParent());
        return AnDomain::getRepository($parent);        
    }
    
    /**
     * Sets the identifiable key
     * 
     * @param string $key The identifiable key
     * 
     * @return LibBaseControllerBehaviorIdentifiable
     */
    public function setIdentifiableKey($key)
    {
        $this->_identifiable_key = $key;
    }
    
    /**
     * Return the identifiable key
     * 
     * @return string
     */
    public function getIdentifiableKey()
    {
        return $this->_identifiable_key;
    }    
	    
    /**
     * Return the object handle
     * 
     * @return string
     */
    public function getHandle()
    {
    	return KMixinAbstract::getHandle();
    }    
}