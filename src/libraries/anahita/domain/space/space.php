<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Domain Space. Implements unit of work and domain entitis states
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainSpace extends KObject 
{
        
	/**
	 * Entity set
	 * 
	 * @var KObjectQueue
	 */
	protected $_entities;
	
	/**
	 * Entity States
	 * 
	 * @var AnObjectArray
	 */
	protected $_states;

	/**
	 * State Machine
	 * 
	 * @var AnDomainRegistryState
	 */
	protected $_state_machine;
	
	/**
	 * Tracks the identifies within a space. Any entity is a unique entity
	 * 
	 * @var array
	 */	
	protected $_identity_map;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
				
		$this->_identity_map = array();
				
		$this->_state_machine	 	 = new AnDomainSpaceState();
		$this->_entities 			 = new KObjectQueue();
		$this->_states				 = new AnObjectArray();
		
		//set the mixer
		$config->mixer = $this;
		
		$this->mixin(new KMixinCommand($config));
		
		//call the validation before commit
		$this->registerCallback('before.commit', array($this, 'validate'));
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
            'command_chain'     => $this->getService('koowa:command.chain'),
	        'enable_callbacks'	=> true,
            'dispatch_events'   => false,
            'event_dispatcher'  => null	                        
	    ));
	
	    parent::_initialize($config);
	}
	
	/**
	 * Validates all the entities wihtin the space
	 *
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	public function validate(KCommandContext $context = null)
	{
	    $context 		  = pick($context, new KCommandContext());
		$entities         = $this->getCommitables();
		$result           = true;
        foreach($entities as $entity)
		{
		    $entity->setError(null);
		    $result = $entity->getRepository()->validate($entity);
			if ( $result === false ) 
			{
			    $context->setError($entity->getError());
			    $context['invalid_entity'] = $entity;
				break;
			}
		}
		return $result !== false;
	}
	
	/**
	 * Commits all the commitable in the space into the store
	 * 
	 * @param  KCommandContext $context Context parameter
	 * 
	 * @return boolean
	 */
	public function commit(KCommandContext $context = null)
	{
		$context 		      = pick($context, new KCommandContext());
		$context->space       = $this;
		if ( $context->result = $this->getCommandChain()->run('before.commit', $context) !== false )
		{
			while( $context->result !== false && count($this->getCommitables()) )
			{
				$entities = $this->getCommitables();
				foreach($entities as $entity)
				{
					$context->result = $entity->getRepository()->commit($entity);
					if ( $context->result === false ) {
					    $context->setError( $entity->getError() );
					    break;
					}
				}
			}
			$context['entities'] = $this->getEntities();
			$this->getCommandChain()->run('after.commit', $context);			
		}
		return $context->result !== false;			
	}

	/**
	 * Set an entity state return whether if the state change was succesful 
	 * 
	 * @param AnDomainEntityAbstract $entity
	 * @param int $new
	 * @return boolean
	 */
	public function setState($entity, $new)
	{
		$current = $this->getState($entity);
		
		if ( $this->_state_machine->stateChanged($entity, $current, $new) === true) 
		{
			if ( $new & AnDomain::STATE_CLEAN ) {
				unset($this->_states[$entity]);
			} else {
				$this->_states[$entity] = $new;
				$this->_insert($entity);
			}
			return true;			
		}
		
		return false;
	}
	
	/**
	 * Return an entity state 
	 * 
	 * @param AnDomainEntityAbstract $entity
	 * @return boolean
	 */
	public function getState($entity)
	{
		if ( isset($this->_states[$entity]) )
			return $this->_states[$entity];
			
		return AnDomain::STATE_CLEAN;
	}

	/**
	 * Switch priority of $child and $parent if the $child_priority is higher than $parent_priority
	 * 
	 * @param AnDomainEntityAbstract $child
	 * @param AnDomainEntityAbstract $parent
	 * @return void
	 */
	public function setDependent($child, $parent)
	{
		$child_priority 	=  $this->_entities->getPriority($child);
		$parent_priority	=  $this->_entities->getPriority($parent);
		//if child is saved before the parent
		if ( $child_priority < $parent_priority ) 
		{
			$this->_entities->setPriority($parent, $child_priority);			
			$this->_entities->setPriority($child,  $parent_priority);
		}
	}
	
	/**
	 * Return an array of entites that are commitable
	 *
	 * @return array
	 */
	public function getCommitables()
	{
		$data = array();
		
		foreach($this->_entities as $entity)
		{
			if ( $entity->state() & AnDomain::STATE_COMMITABLE )
				$data[] = $entity;
		}
				
		return $data;
	}
	
	/**
	 * Inserts an entity into the identity map. It uses the keys to uniquely identifies an entity
	 * 
	 * @param AnDomainEntityAbstract $entity The entity to insert
	 * @param array                  $keys   The keys that uniquely identifies an entity
	 * 
	 * @return void
	 */
	public function insertIdentity($entity, $keys)
	{
		if ( empty($keys) )
			return;

		$this->_insert($entity);
		
		//get all the entity parent classes		
		$classes        = $entity->description()->getUniqueIdentifiers();
		$description	= $entity->description();
		foreach($keys as $key => $value)
		{
			if ( empty($value) ) continue;
			$property = $description->getProperty($key);
			$value = $property->serialize($value);
			$value = implode('', $value);
            
            //use the identifier application as the unique context
			$key   = $entity->getIdentifier()->application.','.$key.','.$value;
            
			foreach($classes as $class)
			{
				if ( !isset($this->_identity_map[$class]) ) 
				{
					$this->_identity_map[$class] = array();
				}				
				$this->_identity_map[$class][$key] = $entity;
			}
		}
	}
	
	/**
	 * Checks to see if an entity of a class with passed in keys exists
	 * in the map. If it exist it will return the entity if not it will return
	 * null
	 * 
	 * @param AnDomainDescriptionAbstract $description The entity description
	 * @param array                       $keys        The keys that uniquely identifies the entity
	 * 
	 * @return AnDomainEntityAbstract
	 */
	public function findIdentity($description, $keys)
	{
		$classes  = $description->getUniqueIdentifiers();
		
		foreach($classes as $class)
		{
		    foreach($keys as $key => $value)
		    {
		        $property = $description->getProperty($key);
		        $value = $property->serialize($value);
		        $value = implode('', $value);
                                
                //use the identifier application as the unique context
		        $key   = $description->getEntityIdentifier()->application.','.$key.','.$value;
                
		        if ( isset($this->_identity_map[$class][$key]) )
		        {
                    //found an entity
		            $entity = $this->_identity_map[$class][$key];
		            //only return an entity if it's still within the space
		            return $this->_entities->contains($entity) ? $entity : null;
		        }
		    }
		}					
		return null;
	}
		
 	/**
 	 * Extracts an entity from the space all together
 	 * 
 	 * @param  AnDomainEntityAbstract
 	 * @return void
 	 */
 	public function extract($entity)
 	{
 		$this->_entities->dequeue($entity);
 		unset($this->_states[$entity]);
 	}
 	
	/**
	 * Return an array of entities
	 * 
	 * @return array
	 */
	public function getEntities()
	{
		return $this->_entities;
	}
	
	/**
	 * Insert an entity to the list of entites. The priortiy of an entity is set to the 
	 * the current total of entities in the queue
	 *
	 * @param AnDomainEntityAbstract $entity
	 * 
	 * @return void
	 */
	protected function _insert($entity)
	{
		//only enqueu if the it hasn't been enqueued before		
		if ( !$this->_entities->getPriority($entity) ) 
		{
			//add the entity to the bottom			
			$priority = count($this->_entities);
			$this->_entities->enqueue($entity, $priority);
		}
	}
}