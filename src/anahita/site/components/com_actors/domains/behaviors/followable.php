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
 * Followable Behavior.
 * 
 * This behavior provides methods for an actor object to add/remove follower and at
 * same time block/unblock unwanted connetions.
 *
 * <code>
 * //fetches a peron with $id
 * $actor  = KService::get('repos://site/actors.actor')->fetch($some_actor_id);
 * $person = KService::get('repos://site/people.person')->fetch($some_person_id);
 * if ( $actor->isFollowable() )
 * { 
 *      //adding the person as a follower
 *      $actor->addFollower($person); 
 *      //if the person is following the actor, then actor is leading
 *      //the person
 *      if ( $actor->leading($person) )
 *          print 'actor is leading the person or person is following the actor'
 *      
 *      $actor->followers //return the actors followers      
 * }
 * </code>
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainBehaviorFollowable extends AnDomainBehaviorAbstract 
{
    /**
     * A flag to whether subscribe to an actor after following
     * or not
     * 
     * @var boolean
     */
    protected $_subscribe_after_follow;
    
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
        
        $this->_subscribe_after_follow = $config->subscribe_after_follow;
    }
        
    /**
     * A boolean flag to temporarily disable resting the stats
     * 
     * @var boolean
     */
    static private $__disable_reset_stats = false;
    
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
		    'subscribe_after_follow'     => $config->mixer->isSubscribable(),
			'attributes'	=> array(
                'allowFollowRequest'   => array('default'=>false),                
                'followRequesterIds'   => array('type'=>'set', 'default'=>'set','write'=>'private'),                 
				'followerCount'  => array('default'=>0,'write'=>'private'),
				'followerIds'    => array('type'=>'set', 'default'=>'set','write'=>'private'),
				'blockedIds'     => array('type'=>'set', 'default'=>'set','write'=>'private')		
			),
			'relationships' => array(
                'requesters' => array(
                    'parent_delete' => 'ignore',
                    'through'   => 'com:actors.domain.entity.request',
                    'target'    => 'com:actors.domain.entity.actor',
                    'child_key' => 'requestee'                
                ),
				'followers'  => array(
				    'parent_delete' => 'ignore',				        
					'through' 	=> 'com:actors.domain.entity.follow',
					'target'	=> 'com:actors.domain.entity.actor',
					'child_key' => 'leader'
				),
				'blockeds' => array(
				    'parent_delete' => 'ignore',
					'through' 	=> 'com:actors.domain.entity.block',
					'target'	=> 'com:actors.domain.entity.actor',
					'child_key' => 'blocker'
				)
			)
		));
		
		parent::_initialize($config);
	}

    /**
     * Add a follow requester to the actor 
     * 
     * @param ComActorsDomainEntityActor $actor The actor to block
     * 
     * @return void
     */ 
    public function addRequester($actor)
    {
        $mixer = $this->_mixer;
        
        self::$__disable_reset_stats = true;
        
        //remove any edges just in case
        $actor->removeFollower($mixer);
        $mixer->removeFollower($actor);
        
        $actor->removeBlocked($mixer);
        $mixer->removeBlocked($actor);
        
        self::$__disable_reset_stats = false;
        
        $edge = $this->getService('repos:actors.request')
            ->findOrAddNew(array(
                'requester'   => $actor ,                   
                'requestee'   => $mixer             
            ));
        
        $edge->save();
        
        $this->resetStats(array($mixer, $actor));
    }
    
    /**
     * Removes an actor from the list of blocked
     * 
     * @param ComActorsDomainEntityActor $actor The actor to block
     * 
     * @return void
     */
    public function removeRequester($actor)
    {
        $data = array(
            'requester'   => $actor ,                   
            'requestee'   => $this->_mixer 
        );
        
        $this->getService('repos:actors.request')->destroy($data);
        
        $this->resetStats(array($this->_mixer, $actor));
    }
        
	/**
	 * Add a follower to the actor 
	 * 
	 * @param ComActorsDomainEntityActor $actor The actor to block
	 * 
	 * @return void
	 */	
	public function addFollower($actor)
	{
		//if A adds B as follower, then A must remove B as a blocked
		//@TODO should not be able to follow if blocked 		
    	$mixer = $this->_mixer;
    	
        //actor is becoming the mixer follower
        //hence mixer is a leader. therefore any pending
        //request from mixer to the actor must be replaced
        //with a follow
         
        if ( $actor->requested($mixer) ) 
        {
            //if mixer has also been requested 
            //prevents infite nesting 
            if ( $mixer->requested($actor) ) {
               $mixer->followRequesterIds->offsetUnset($actor->id);
            }
            
            $actor->addFollower($mixer);
        }
        
        self::$__disable_reset_stats = true;
        
    	$actor->removeBlocked($mixer);
    	$mixer->removeBlocked($actor);
        
        //just in case
        $actor->removeRequester($mixer);
        $mixer->removeRequester($actor);        
    	
        self::$__disable_reset_stats = false;
        
        //add a subscriber
        if ( $this->_subscribe_after_follow ) {
            $mixer->addSubscriber($actor);
        }
                
		$edge = $this->getService('repos:actors.follow')
		    ->findOrAddNew(array(
		        'leader'	  => $mixer,
		        'follower'	  => $actor		        
            ));

		$edge->save();
		
		$this->resetStats(array($mixer, $actor));
		
		return $edge;
	}
		
	/**
	 * Remove an actor to from list of the actor followers. If the 
	 * actor is subscribable then it will remove the actor from the list
	 * of it's subscribers 
	 * 
	 * @param ComActorsDomainEntityActor $actor The actor to block
	 * 
	 * @return void
	 */	
	public function removeFollower($actor)
	{   		  
        
        $mixer = $this->_mixer;

        if ( $mixer->isSubscribable() ) {
            $mixer->removeSubscriber($actor);            
        }
          
        //remove the follower as subscriber from any node that's owned
        //mixer
        
        //lets find all the nodes that actor is subscribed to
        $query        = $this->getService('repos://site/base.subscription')->getQuery()
                        ->subscriber($actor)
                        ->where('subscribee.id','IN', $this->getService('repos:base.node')->getQuery()->columns('id')->where('owner_id','=',$mixer->id))
        ;
        
        $subscribers = $query->disableChain()->fetchValues('subscribee.id');
        
        if ( count($subscribers) )
        {
            $this->getService('repos://site/base.node')
            ->update("subscriber_ids = @remove_from_set(subscriber_ids,{$actor->id}), subscriber_count = @set_length(subscriber_ids)",
            $subscribers
            );
            
            $this->getService('repos://site/base.subscription')
            ->destroy($query);
        }
             			        
        if ( $mixer->isAdministrable() ) {
            $mixer->removeAdministrator($actor);            
        }
		 
		$this->getService('repos:actors.follow')
		    ->destroy(array(
	            'follower'	  => $actor,
	            'leader'	  => $mixer		        
            ));
		
		$this->resetStats(array($mixer, $actor));
	}
	
    /**
     * Adds an actor to a list of blockes
     * 
     * @param ComActorsDomainEntityActor $actor The actor to block
     * 
     * @return void
     */
    public function addBlocked($actor)
    {   
    	//if A blocks B, then A must remove B as a follower 
    	//need to keep track of this since the mixin is a singleton
    	$mixer = $this->_mixer;
    	
        self::$__disable_reset_stats = true;
        
        $actor->removeFollower($mixer);
    	$mixer->removeFollower($actor);
		//just in case
        $actor->removeRequester($mixer);
        $mixer->removeRequester($actor);
        
        self::$__disable_reset_stats = false;
                
		$edge = $this->getService('repos:actors.block')
		    ->findOrAddNew(array(
				'blocker'	  => $mixer ,					
				'blocked'	  => $actor		        
            ));
		
		$edge->save();
		
		$this->resetStats(array($mixer, $actor));
			
		return $edge;
    }    
    
    /**
     * Removes an actor from the list of blocked
     * 
     * @param ComActorsDomainEntityActor $actor The actor to block
     *  
     * @return void
     */
    public function removeBlocked($actor)
    {
		$data = array(
			'blocker'	  => $this->_mixer,		
			'blocked'	  => $actor
		);
		
		$this->getService('repos:actors.block')->destroy($data);		
		$this->resetStats(array($this->_mixer, $actor));
    }

	/**
	 * Return true if the actor is following the mixer else return false	 
	 * 
	 * @param ComActorsDomainEntityActor $actor The actor to block
	 * 
	 * @return boolean 
	 */	
	public function leading($actor)
	{
		return $this->_mixer->followerIds->offsetExists($actor->id);
	}
    
    /**
     * Return true if the actor has been requested to be followed 
     * 
     * @param ComActorsDomainEntityActor $requester The requested
     * 
     * @return boolean 
     */ 
    public function requested($actor)
    {
        return $this->_mixer->followRequesterIds->offsetExists($actor->id);
    }    
		
	/**
	 * Return true if the mixer is blocking another actor else return false 	 
	 * 
	 * @param ComActorsDomainEntityActor $actor The actor to block
	 * 
	 * @return boolean 
	 */	
	public function blocking($actor)
	{
		return $this->_mixer->blockedIds->offsetExists($actor->id);
	}

	/**
	 * Adds a filter to the query based on the access mode
	 *
	 * @param  KCommandContext $context
	 * 
	 * @return void
	 */
	protected function _beforeRepositoryFetch(KCommandContext $context)
	{
	    if ( KService::has('viewer') )
	    {
	        $query		= $context->query;
	        $repository = $query->getRepository();
	        $viewer     = get_viewer();
	        if ( $viewer->id ) {
	            $query->where("IF( FIND_IN_SET({$viewer->id}, @col(blockedIds)), 0, 1)");
	        }	        
	    }
	}
	
	/**
	 * Reset the all graph information for the actors
	 *
	 * @param array $actors Array of actors
	 *
	 * @return void
	 */
	public function resetStats($actors)
	{
        //if reset stats is disabled then return
        if ( self::$__disable_reset_stats === true )
            return;
            
	    foreach($actors as $actor)
	    {
	        if ( $actor->isFollowable() )
	        {
	            $follower_ids 	= $actor->followers->getQuery(true,true)->fetchValues('id');
	            $blocked_ids	= $actor->blockeds->getQuery(true,true)->fetchValues('id');
                $requester_ids  = $actor->requesters->getQuery(true,true)->fetchValues('id');
	            $actor->set('followerCount', count($follower_ids));
	            $actor->set('followerIds'  , AnDomainAttribute::getInstance('set')->setData($follower_ids));
	            $actor->set('blockedIds', 	 AnDomainAttribute::getInstance('set')->setData($blocked_ids));
                $actor->set('followRequesterIds',  AnDomainAttribute::getInstance('set')->setData($requester_ids));
	        }
	
	        if ( $actor->isLeadable() )
	        {
	            $leader_ids	 	= $actor->leaders->getQuery(true,true)->fetchValues('id');
	            $blocker_ids	= $actor->blockers->getQuery(true,true)->fetchValues('id');
	            $mutual_ids	 	= array_intersect($leader_ids, $follower_ids);
	            $actor->set('leaderCount' , count($leader_ids));
	            $actor->set('leaderIds'	  , AnDomainAttribute::getInstance('set')->setData($leader_ids));
	            $actor->set('blockerIds'  , AnDomainAttribute::getInstance('set')->setData($blocker_ids));
	            $actor->set('mutualIds'   , AnDomainAttribute::getInstance('set')->setData($mutual_ids));
	            $actor->set('mutualCount' , count($mutual_ids));
	        }
	    }
	}	
}