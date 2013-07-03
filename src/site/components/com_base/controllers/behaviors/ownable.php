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
 * Ownable Behavior. It feches an owner wherenever there's an oid
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerBehaviorOwnable extends KControllerBehaviorAbstract
{
    /**
     * Default owner
     * 
     * @var mixed
     */
    protected $_default;
    
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
               
        $this->_default = $config['default'];
        
        //set the default actor
        $this->setActor($this->_default);
        
        //set the identifiable key. By default its set to oid
        $this->_identifiable_key = $config->identifiable_key;
                 
        //$this->_default ? $this->_default->id : null                                                        
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
            'identifiable_key' => 'oid',
            'default'          => null,
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
        
		if ( $parts[0] == 'before' ) {
			$this->_fetchOwner($context);
		}
		
	    parent::execute($name, $context);
    }
   	
    /**
     * If the context->data actor is not already set them set the owner to the data 
     * before controller add. 
     * 
     * @param KCommandContext $context
     * 
     * @return boolean
     */
    protected function _beforeControllerAdd(KCommandContext $context)
    {
        if ( !$context->data['owner'] instanceof ComActorsDomainEntityActor )
        {
            if  ( $this->getRepository()->hasBehavior('ownable') ) {
                $context->data['owner'] = $this->actor;
            }
        }
    }
   	
    /**
     * Set the actor conect
     * 
     * @param ComActorsDomainEntiyActor $actor Set the actor context
     * 
     * @return ComBaseControllerBehaviorOwnable
     */
    public function setActor($actor)
    {
       $this->_mixer->actor = $actor;
       return $this;
    }
    
    /**
     * Return the actor context
     * 
     * @return ComActorsDomainEntiyActor
     */
    public function getActor()
    {
        return $this->_mixer->actor;
    }
    
    /**
     * Fetches an entity
     *
     * @param KCommandContext $context
     * 
     * @return ComActorsDomainEntityActor
     */
    protected function _fetchOwner(KCommandContext $context)
    {
        $actor = pick($this->getActor(), $this->_default);
        $value = $this->{$this->getIdentifiableKey()};
        
        if ( $value ) 
        {
            if ( $value == 'viewer' )  {
                $actor = get_viewer();
            }
			elseif ( !is_numeric($value) ) {
				$actor = $this->getService('repos://site/people.person')->fetch(array('username'=>$value));
			}
            else {
                $actor = $this->getService('repos://site/actors.actor')->fetch((int)$value);
            }
            
            //guest actor can never be a context actor                
            if ( is_person($actor) && $actor->guest() ) {
                $actor = null;
            }
                    
            //set the data owner to actor.
            $context->data['owner'] = $actor;  
            
            if ( !$actor ) {
                throw new LibBaseControllerExceptionNotFound('Owner Not Found');
            }                      
        }
        
        $this->setActor($actor);
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