<?php

/** 
 * LICENSE: ##LICENSE##
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
	protected $_identity_map = array();
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
				
		$this->_state_machine	 	 = new AnDomainSpaceState();
		$this->_entities 			 = $this->getService('anahita:domain.space.queue');
		$this->_states				 = new AnObjectArray();
	}
	
    /**
     * Validates all the entities in the space. Return true if all the entities
     * pass the validation or a set of entities that failed the validation.If a $failed variable
     * is passed by reference, then a set of failed entities will be returend
     * 
     * @param mixed &$failed Return the failed set
     * 
     * @return boolean Return if all the entities passed the validations
     */
    public function validateEntities(&$failed = null)
    {
        $restult  = true;
        $failed   = new AnObjectSet();
        
        foreach($this->getCommitables() as $entity)
        {
            $restult = $entity->getRepository()->validate($entity);
               
            if ( $restult === false ) {
               $failed->insert($entity);
            }
        }
        
        return $failed->count() === 0;
    }
        
    /**
     * Commits all the entities in the space. Return true if all the entities
     * commit succesfully or a set of entities that failed the commit. If a $failed variable
     * is passed by reference, then a set of failed entities will be returend
     * 
     * @param mixed &$failed Return the failed set
     * 
     * @return boolean Return if all the entities passed the validations
     */
    public function commitEntities(&$failed = null)
    {                
        $result  = $this->validateEntities($failed);
        
        while($result && count($entities = $this->getCommitables()))
        {
            foreach($entities as $entity)
            {
                $result = $entity->getRepository()->commit($entity);
                
                if ( $result === false ) {
                   $failed->insert($entity);
                }
            }
        }
                
        return $failed->count() === 0;             
    }

	/**
	 * Set an entity state return whether if the state change was succesful 
	 * 
	 * @param AnDomainEntityAbstract $entity Domain entity
	 * @param int                    $new    New state
     * 
	 * @return boolean Return whether the state for the entity has been set sucesfully
	 */
	public function setEntityState($entity, $new)
	{
		$current = $this->getEntityState($entity);
		
		if ( $this->_state_machine->stateChanged($entity, $current, $new) === true) 
		{
			if ( $new & AnDomain::STATE_CLEAN ) {
				unset($this->_states[$entity]);
			} else {
				$this->_states[$entity] = $new;
				$this->insertEntity($entity);
			}
			return true;			
		}
		
		return false;
	}
	
	/**
	 * Return an entity state 
	 * 
	 * @param AnDomainEntityAbstract $entity Domain entity
     * 
	 * @return boolean
	 */
	public function getEntityState($entity)
	{
		if ( isset($this->_states[$entity]) )
			return $this->_states[$entity];
			
		return AnDomain::STATE_CLEAN;
	}

	/**
	 * Set the save order 
	 * 
	 * @param AnDomainEntityAbstract $entity1 Lower priorty index (Higher) domain entity
	 * @param AnDomainEntityAbstract $entity2 Higher priorty index (Lower) domain entity
     * 
	 * @return void
	 */
	public function setSaveOrder($entity1, $entity2)
	{
        //lower priorty index means it's saved first (higher priorty)
        //so if $entity1 has lower priorty index (higher priority) than $entity2
        //then $entity1 is saved before $entity2

        //higher prioriry index means lower priority
        $lower_priority  = max($this->_entities->getPriority($entity1), $this->_entities->getPriority($entity2));
        
        //lower priority index means higher priority
        $higher_priority = min($this->_entities->getPriority($entity1), $this->_entities->getPriority($entity2));
        
        $this->_entities->setPriority($entity1,  $higher_priority);
        $this->_entities->setPriority($entity2,  $lower_priority);
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
			if ( $entity->getEntityState() & AnDomain::STATE_COMMITABLE )
				$data[] = $entity;
		}
				
		return $data;
	}
	
	/**
	 * Inserts an entity into the identity map. It uses the keys to uniquely identifies an entity
	 * 
	 * @param AnDomainEntityAbstract $entity      The entity to insert
	 * @param array                  $identifiers An array of identifiying keys that uniquely identifies an entity
	 * 
	 * @return void
	 */
	public function insertEntity($entity, $identifiers = array())
	{
        //check if an entity has already been added
        //if not then add it to the bottom
		if ( !$this->_entities->getPriority($entity) ) {
            $priority = count($this->_entities);
            $this->_entities->enqueue($entity, $priority);            
        }
		
		//get all the entity parent classes		
		$classes        = $entity->getEntityDescription()->getUniqueIdentifiers();
		$description	= $entity->getEntityDescription();
        
		foreach($identifiers as $key => $value)
		{
			if ( empty($value) ) 
                continue;
                
			$property = $description->getProperty($key);
			$value = $property->serialize($value);
			$value = implode('', $value);
            
            //use the identifier application as the unique context
			$key   = $entity->getIdentifier()->application.$key.$value;
            
			foreach($classes as $class)
			{
				if ( !isset($this->_identity_map[$class]) ) 
					$this->_identity_map[$class] = array();
                    
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
	 * @param array                       $identifiers The keys that uniquely identifies the entity
	 * 
	 * @return AnDomainEntityAbstract
	 */
	public function findEntity($description, $identifiers)
	{
		$classes  = $description->getUniqueIdentifiers();
		
		foreach($classes as $class)
		{
		    foreach($identifiers as $key => $value)
		    {
		        $property = $description->getProperty($key);
		        $value = $property->serialize($value);
		        $value = implode('', $value);
                                
                //use the identifier application as the unique context
		        $key   = $description->getEntityIdentifier()->application.$key.$value;
                
		        if ( isset($this->_identity_map[$class][$key]) )
                {
                    //found an entity
		            $entity = $this->_identity_map[$class][$key];

		            //only return an entity if it's still within the space
		            if ( !$this->_entities->contains($entity) ) {
		            	return null;
		            }		            
		            
		            //if the description we are using is the parent of the found entity
		            //if not then we must have found a different entity with the common parent
		            //as the caller repository
		            if ( !is_a($entity, $description->getEntityIdentifier()->classname) ) {
		            	return null;
		            }
		            
		            return $entity;
		        }
		    }
		}					
		return null;
	}
		
 	/**
 	 * Extracts an entity from the space all together
 	 * 
 	 * @param AnDomainEntityAbstract $entity Extracts an entity from the domain space
     * 
 	 * @return void
 	 */
 	public function extractEntity($entity)
 	{
 		$this->_entities->dequeue($entity);
 		unset($this->_states[$entity]);
 	}
 	
	/**
	 * Return an array of entities. If the repository is set then return entities for the 
	 * repository
	 * 
	 * @param AnDomainRepositoryAbstract $repository If the repository is set then return the entities
	 * 												 for the $repository
	 * @return KObjectQeueue
	 */
	public function getEntities($repository = null)
	{
		return $repository ? $this->_entities->getRepositoryEntities($repository) : 
					$this->_entities;
	}
}