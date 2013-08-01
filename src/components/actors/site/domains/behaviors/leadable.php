<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Leadable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainBehaviorLeadable extends AnDomainBehaviorAbstract 
{
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
			'attributes'	=> array(
				'leaderCount' => array('default'=>0,'write'=>'private'),
				'leaderIds'   => array('type'=>'set', 'default'=>'set','write'=>'private'),
				'blockerIds'  => array('type'=>'set', 'default'=>'set','write'=>'private'),
				'mutualIds'	  => array('type'=>'set', 'default'=>'set','write'=>'private'),
				'mutualCount' => array('default'=>0,'write'=>'private')
			),
			'relationships' => array(
				'blockers' => array(
					'through' 	        => 'com:actors.domain.entity.block',
					'parent_delete'		=> 'ignore',
					'child_key' => 'blocked',
					'target'	=> 'com:actors.domain.entity.actor',					
				),
				'leaders' => array(
				    'parent_delete'	=> 'ignore',
					'through' 	=> 'com:actors.domain.entity.follow',
					'child_key' => 'follower',				
					'target'	=> 'com:actors.domain.entity.actor',
				)
			)
		));
		
		parent::_initialize($config);
	}
		
	/**
	 * Return this person common leader with another person 
	 * 
	 * @param ComActorsDomainEntityActor $actor Actor for which to get the common leaders
	 * 
	 * @return AnDomainEntitysetDefault
	 */
	public function getCommonLeaders($actor)
	{
		if ( !isset($this->__common_leaders) )
		{				
			$ids    = array_intersect($this->leaderIds->toArray(), $actor->leaderIds->toArray());
			$ids[]  = -1;
			$query	= $this->getService('repos:actors.actor')
						->getQuery()->where('id','IN', $ids);


			$this->__common_leaders = $query->toEntitySet();
		}
		
		return $this->__common_leaders;
	}
	
	/**
	 * Return the mutual followers
	 * 
	 * @return AnDomainEntitysetAbstract
	 */
	public function getMutuals()
	{
		if ( !isset($this->__mutuals) )
		{
			$ids   = array_intersect($this->leaderIds->toArray(), $this->followerIds->toArray());
			
			$query	= $this->getService('repos://site/people.person')
						->getQuery()->where('id','IN',$ids);
												
			$this->__mutuals = $query->toEntitySet();
		}
				
		return $this->__mutuals;
	}
		
	/**
	 * Return true if the both the mixer and person is following each other 
	 * else it returns false;
	 * 
	 * @param ComPeopleDomainEntityPerson $person Person object
	 * 
	 * @return boolean
	 */
	public function mutuallyLeading($person)
	{
		return $this->leading($person) && $this->following($person);
	}

	/**
	 * Return true if the mixer is following the person else return false 	 
	 * 
	 * @param ComActorsDomainEntityActor $actor Actor object
	 * 
	 * @return boolean 
	 */	
	public function following($actor)
	{
		return $this->_mixer->leaderIds->offsetExists($actor->id);
	}	

	/**
	 * Return true if the mixer is blocked by the person else return false 	 
	 * 
	 * @param  ComActorsDomainEntityActor $actor Actor object
	 * 
	 * @return boolean 
	 */	
	public function blocked($actor)
	{
		return $this->_mixer->blockerIds->offsetExists($actor->id);
	}	
}