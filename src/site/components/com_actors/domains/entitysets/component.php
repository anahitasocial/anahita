<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Request edge represents a follow request between two actors. 
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainEntitysetComponent extends AnObjectDecorator
{
	/**
	 * The actor whos assigned components contains in this set
	 * 
	 * @var ComActorsDomainEntityActor
	 */
	protected $_actor;
	
	/**
	 * Constructor.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	public function __construct(KConfig $config)
	{
		if ( !$config->actor ) {
			throw new KException('Actor object is missing');	
		}
		
		$this->_actor = $config->actor;
		
		parent::__construct($config);	
				
		$query 	= $this->getService($config->repository)
					->getQuery()
					->bind('actorid', $this->_actor->id)
					->bind('components_set_to_never',
							$this->getService('repos://site/components.assignment')
							->getQuery()
							->columns('id')
							->where('@col(actortype) = :actortype AND @col(access) = :never'))
							->bind('actortype', (string)$this->_actor->getEntityDescription()->getInheritanceColumnValue()->getIdentifier())
							->bind('never',     ComComponentsDomainBehaviorAssignable::ACCESS_NEVER)
							->bind('always',    ComComponentsDomainBehaviorAssignable::ACCESS_ALWAYS)
							->bind('optional',  ComComponentsDomainBehaviorAssignable::ACCESS_OPTIONAL)
							;
	
		$query->link('repos://site/components.assignment','@col(assignment.component) = @col(component)',array('type'=>'weak'))
				->enabled(true)
				->where('@col(id) NOT IN (:components_set_to_never)')
				;		
		if ( $config->can_enable ) {									
			$query->where('@col(assignment.actortype) = :actortype AND @col(access) = :optional');
		} else {
			$query->where('((@col(assignment.actortype) = :actortype AND @col(access) = :always) OR @col(assignment.actor.id)  = :actorid OR @col(option) = "com_stories")');
		}
		
		$config['query'] = $query;
		
		$this->_object = $this->getService('anahita:domain.entityset', KConfig::unbox($config));
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
			'data' 		 => null,
			'repository' => 'repos://site/components.component'			 
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KObjectSet::insert()
	 */
	public function insert($component)
	{
		if ( is_string($component) ) {
			$component = $this->getRepository()->fetch(array('component'=>$component));
		}
		
		if ( $component->isAssignable() ) {
			$assignment = $component->assignments->findOrAddNew(array('actor'=>$this->_actor));			
		}
		
		if ( $this->isLoaded() ) {
			parent::insert($component);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KObjectDecorator::__call()
	 */
	public function __call($method, $arguments)
	{
		return call_object_method($this->getObject(), $method, $arguments);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KObjectSet::extract()
	 */
	public function extract($component)
	{
		if ( is_string($component) ) {
			$component = $this->getRepository()->fetch(array('component'=>$component));
		}
		
		if ( $component->isAssignable() ) {
			$assignment = $component->assignments->find(array('actor'=>$this->_actor));
			if ( $assignment ) {
				$assignment->delete();
			}
		}
		
		if ( $this->isLoaded() ) {
			parent::extract($component);
		}
	}
}