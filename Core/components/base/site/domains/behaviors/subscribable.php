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
 * Subscribable Behavior
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseDomainBehaviorSubscribable extends AnDomainBehaviorAbstract
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
				'subscriberCount'=> array('default'=>0,'write'=>'private'),
				'subscriberIds'	 => array('type'=>'set', 'default'=>'set','write'=>'private')					
			),
			'relationships'	=> array(
				'subscriptions' => array('child'=>'com:base.domain.entity.subscription',   'child_key'=>'subscribee', 'parent_delete'=> 'ignore')
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Subscribe the author to the node
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _afterEntityInsert(KCommandContext $context)
	{
		if ( !empty($this->author) )
			$this->addSubscriber($this->author);
			
		//if the owner is notifiable (can be notified) then
        //add it as a subscriber
        if ( $this->isOwnable() && $this->owner->isNotifiable() )
			$this->addSubscriber($this->owner);
	}
	
	/**
	 * Return whether the person's is subsribed or not 
	 *
	 * @param ComPeopleDomainEntityPerson $person Subscriber
	 * 
	 * @return boolean
	 */
	public function subscribed($subscriber)
	{
		return ($subscriber->id && $this->subscriberIds->offsetExists($subscriber->id));		
	}
	
	/**
	 * Adds a person as the subscriber
	 *
	 * @param  ComPeopleDomainEntityPerson $person Subscriber
	 * 
	 * @return void
	 */
	public function addSubscriber($person)
	{
	    $this->subscriptions->findOrAddNew(array(
	        'subscriber' => $person
        ))->setData(array(
            'component' => $this->component,
            'author'	=> $person                
        ), AnDomain::ACCESS_PROTECTED);
	}
	
	/**
	 * Removes a person from the list of subscriber of the node
	 *
	 * @param  ComPeopleDomainEntityPerson $person Subscriber
	 * 
	 * @return void
	 */
	public function removeSubscriber($person)
	{
        $mixer        = $this->_mixer;
        $subscription =  $this->subscriptions->find(array('subscriber'=>$person,'subscribee'=>$mixer));
        if ( $subscription ) {
            $this->subscriptions->extract($subscription);
            $this->resetStats(array($mixer));
        }
	}

	/**
	 * Reset subscriptions stats
	 *
	 * @param array $entities
	 * 
	 * @return void
	 */
	public function resetStats(array $entities)
	{
	    foreach($entities as $entity)
	    {
	        $ids = $entity->subscriptions->getQuery()->disableChain()->fetchValues('subscriber.id');
	        $entity->set('subscriberCount',		count($ids));
	        $entity->set('subscriberIds', 		AnDomainAttribute::getInstance('set')->setData($ids));
	    }
	}
}