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
 * Entity state machine
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainSpaceState extends KObject 
{
	/**
	 * State Machine
	 * 
	 * @var Array
	 */
	protected $_machine;
	
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct()
	{		
		$clean 		= AnDomain::STATE_CLEAN;
		$new   		= AnDomain::STATE_NEW;		
		$modified	= AnDomain::STATE_MODIFIED;
		$deleted	= AnDomain::STATE_DELETED;
		
		//internal state
		$destroyed  = AnDomain::STATE_DESTROYED;
		$updated	    = AnDomain::STATE_UPDATED;
		$inserted	= AnDomain::STATE_INSERTED;
		
		//these states are only the possible states that can be set by 
		//the entity itself throught its reset/delete/update API
		$this->_machine = array(
		//clean
			$clean.'=>'.$deleted      => array('validateId',	'validateDelete'),
			$clean.'=>'.$modified     => array('validateId',	'validateChange'),
		//new
			$new.'=>'.$clean          => array('resetrelationships'),
			$new.'=>'.$deleted        => array('reset', false),
			$new.'=>'.$modified       => array(false),						
		//modified
			$modified.'=>'.$deleted   => array('validateDelete'),
			$modified.'=>'.$modified  => array('validateChange'),
		//inserted
			$inserted.'=>'.$deleted   => array('validateDelete'),
			$inserted.'=>'.$modified  => array('validateChange'),			
		//updated
			$updated.'=>'.$deleted    => array('validateDelete'),
			$updated.'=>'.$modified   => array('validateChange'),
		//deleted
			$deleted.'=>'.$clean 	  => array(false),
			$deleted.'=>'.$modified   => array(false),
//			$deleted.'=>'.$deleted 	  => array(false),	
		//destoryed
			$destroyed.'=>'.$clean    => array(false),
			$destroyed.'=>'.$modified => array(false),
			$destroyed.'=>'.$deleted  => array(false)	
		);
	}
	
	/**
	 * Run a state change into the state machine
	 *
	 * @param AnDomainEntityAbstract $entity
	 * @param int $current
	 * @param int $new
	 */
	public function stateChanged($entity, $current, $new)
	{
		$key = $current.'=>'.$new;
		
		if ( !isset($this->_machine[$key]) )
			return true;
			
		$callbacks = $this->_machine[$key];
		
		foreach($callbacks as $callback) {
			$result = $callback;
			if ( is_string($callback) )
				$result = $this->{'_'.$callback}($entity);
			if ( $result === false )
				return false;	
		}
		
		return true;
	}
	
	/**
	 * Resets an entity
	 * 
	 * @return void
	 */
	protected function _reset($entity)
	{
		$entity->reset();
	}
	
	/**
	 * Resets an entity
	 * 
	 * @return void
	 */
	protected function _resetrelationships($entity)
	{
		$entities	= clone $entity->getRepository()->getSpace()->getEntities();
		
		//reset all the entities that have a belongs to or one to one relationship
		//with the $entity. Because since $entity is not being saved
		//nor should any child that requires the parent $entity value 
		foreach($entities as $child) 
		{
			if ( $child->eql($entity) ) continue;
			
			$relationships = $child->getEntityDescription()->getRelationships();
			
			foreach($relationships as $relationship) 
			{
				//child has a one to one or belongs to relationship to the entity (parent) 
				if ( $relationship->isOneToOne() || $relationship->isManyToOne() )
				{
					$value = $child->get($relationship->getName());
					
					if ( $entity->eql($value) ) {
						$child->reset();
					}
				}
			}
		}
			
	}
	
	/**
	 * Validates if the entity uniques are present
	 * 
	 * @return boolean
	 */
	protected function _validateId($entity)
	{
	    return $entity->persisted();
	}	
		
	/**
	 * Validates if there are any modified properties for an entity
	 * 
	 * @return boolean
	 */
	protected function _validateChange($entity)
	{
		return count($entity->getModifiedData()) > 0;		
	}

	/**
	 * Validates and apply delete rules for an entity
	 *
	 * @param $entity
	 */
	protected function _validateDelete($entity)
	{
		$relationships = $entity->getEntityDescription()->getRelationships();
	
		foreach($relationships as $name => $relationship) 
		{
			if ( $relationship->isManyToOne() || $relationship->isManyToMany() )
				continue;

				
			if ( $relationship->getDeleteRule()    ==  AnDomain::DELETE_CASCADE
				 || $relationship->getDeleteRule() ==  AnDomain::DELETE_DESTROY
			) 
			{
				$property 	  = $relationship->getName();
				$entities 	  = $entity->getData($property);
				
				if ( !$entities ) 
					continue;
				//someone else is responsible for deleteing the relations
				//good for mass deletion. i.e. node and edges
				if ( $relationship->getDeleteRule() == AnDomain::DELETE_IGNORE)
				    continue;		
				else if ( $relationship->getDeleteRule() == AnDomain::DELETE_DESTROY)
				{
				    if (  $relationship->isOneToOne() )
				        $query = AnDomainQuery::getInstance($relationship->getChildRepository(), $entities->getIdentityId());
				    else 
				        $query = $entities->getQuery();
				    
					$relationship->getChildRepository()->destroy($query);
					continue;
				}
				else 
				{
					if ( $relationship->isOneToOne() )
						$entities = array($entities);
						
					foreach($entities as $entity) 
					{
						//if the cascading fails for the related entities then
						//nullify the property in the failed entity 
						if ( !$entity->delete() && $entity->getObject() ) 
							$entity->set($relationship->getChildKey(), null);
					}
				}
				
			} else if ( $relationship->getDeleteRule() == AnDomain::DELETE_DENY ) 
			{
				//don't state change if there at least one entity left
				$count = $entity->getData( $relationship->getName() )->limit(0, 0)->getTotal();
				
				if ( $count > 0 ) 
				{
					$entity->reset();
					return false;
				}
			} else if ( $relationship->getDeleteRule() == AnDomain::DELETE_NULLIFY ) 
			{
			    //@TODO you should set the values to null directly rather
			    //then instantiating them
				$entities 		= $entity->getData( $relationship->getName() )->fetchSet();
				$property	    = $relationship->getChildKey();
				foreach($entities as $entity) {
					$entity->set($property, null);
				}
			}
		}

		return true;
	}	
}