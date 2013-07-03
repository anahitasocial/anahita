<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * A sharable behavior alllows a node to be shared with several owners
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComBaseDomainBehaviorSharable extends AnDomainBehaviorAbstract
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
			'relationships' => array(
				'owners'  => array(
				    'parent_delete' => 'ignore',
					'through'  	    => 'com:base.domain.entity.ownership', 
					'as'	   	    => 'ownership',
					'child_key'     => 'ownable', 
					'target'	    => 'com:actors.domain.entity.actor'
				)
			),
			'attributes' => array(
				'sharedOwnerIds' 	 => array('type'=>'set',   'default'=>'set','write'=>'private'),
				'sharedOwnerCount'	 => array('type'=>'integer','write'=>'private'),
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Adds an owner 
	 * 
	 * @param ComActorsDomainEntityActor $owner The owner actor
	 * 
	 * @return void
	 */
	public function addOwner($owner)
	{
		$ownership = $this->owners->insert($owner);
		return $ownership;
	}
	
 	/**
	 * Remove an owner
	 * 
	 * @param ComActorsDomainEntityActor $owner The owner actor
	 * 
	 * @return void
	 */
	public function removeOwner($owner)
	{
		if ( !$this->owners->extract($owner) ) {
			$this->resetStats(array($this->_mixer));
		}
	}	
	
    /**
	 * Reset share stats
	 *
	 * @param array $entities
	 * 
	 * @return void
	 */
	public function resetStats(array $entities)
	{
		foreach($entities as $entity)
		{
		    $ids	= $this->getService('repos:base.ownership')->getQuery()->ownable($entity)->disableChain()->fetchValues('owner.id');
		    $entity->set('sharedOwnerCount',	count($ids));
		    $entity->set('sharedOwnerIds', 		AnDomainAttribute::getInstance('set')->setData($ids));		    
		}
	}
 }