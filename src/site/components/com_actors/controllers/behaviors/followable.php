<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Followable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerBehaviorFollowable extends KControllerBehaviorAbstract
{
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
        
        $config->mixer->registerCallback(
            array('before.unfollow','before.follow', 
            	  'before.lead', 'before.unlead',
                  'before.addrequest','before.deleterequest',
                  'before.block','before.unblock'), 
                  array($this, 'getActor'));
    }
    
    /**
     * Add a set of actors to the owners list of requester.
     * 
     * @param KCommandContext $context Context Parameter
     * 
     * @return void
     */
    protected function _actionAddrequest(KCommandContext $context)
    {        
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
        $this->getItem()->addRequester($this->actor);
        
        $this->createNotification(array('subject'=>$this->actor,'target'=>$this->getItem(),'name'=>'actor_request'));
        
        return $this->getItem();        
    }
    
    /**
     * Add a set of actors to the owners list of requester.
     * 
     * @param KCommandContext $context Context Parameter
     * 
     * @return void
     */
    protected function _actionDeleterequest(KCommandContext $context)
    {
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
        $this->getItem()->removeRequester($this->actor);
    }    
            
	/**
	 * Add $data->actor to the current actor resource. status is set to 
     * KHttpResponse::RESET_CONTENT;
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return void 
	 */
	protected function _actionFollow(KCommandContext $context)
	{
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
		if(!$this->getItem()->leading($this->actor))
		{
		    $this->getItem()->addFollower($this->actor);
		    
	    	$story = $this->createStory(array(
	    		'name' => 'actor_follow',
	        	'subject' => $this->actor,
	        	'owner' => $this->actor,
	        	'target' => $this->getItem()
	    	));

	    	if($this->getItem()->isAdministrable())
	    		$subscribers = $this->getItem()->administratorIds->toArray();
	    	else
	    		$subscribers = array($this->getItem()->id);
	    	
	    	$this->createNotification(array(
	    		'name' => 'actor_follow',
	    		'subject' => $this->actor, 
	    		'target' => $this->getItem(),
	    		'subscribers' => $subscribers
	    	));
		}
        
        return $this->getItem();
	}
	
	/**
	 * Add a person to the. The data passed is set my the receiver controller::getCommandChain()::getContext()::data
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return void
	 */
	protected function _actionUnfollow(KCommandContext $context)
	{
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
		$this->getItem()->removeFollower($this->actor);
        
		return $this->getItem();
	}
	
	/**
	 * Add a leader to this actor
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return ComActorsDomainEntityActor object 
	 */
	protected function _actionLead(KCommandContext $context)
	{
		$this->getResponse()->status = KHttpResponse::RESET_CONTENT;
		
		if(!$this->getItem()->following($this->actor))
		{
		    $this->getItem()->addLeader($this->actor);
		    
	    	$this->createStory(array(
	    		'name' => 'actor_follower_add',
	    		'owner' => $this->actor,
	    		'subject' => $this->viewer,
	    		'object' => $this->getItem(),
	    		'target' => $this->actor
	    	));
	    		
	    	if($this->actor->isAdministrable())
	    		$subscribers = $this->actor->administratorIds->toArray();
	    	else
	    		$subscribers = array($this->actor->id);
	    		
	    	$this->createNotification(array(
	    		'name' => 'actor_follower_add',
	    		'subject' => $this->viewer,
	    		'object' => $this->getItem(),
	    		'target' => $this->actor,
	    		'subscribers' => $subscribers
	    	));
		}
        
        return $this->getItem();
	}
		
	/**
	 * Remove a leader from this actor
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return ComActorsDomainEntityActor object
	 */
	protected function _actionUnlead(KCommandContext $context)
	{
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
		$this->getItem()->removeLeader( $this->actor );
        
		return $this->getItem();
	}
    
	/**
     * The viewers blocks the actor
     *
     * @param KCommandContext $context Context parameter
     *
     * @return ComActorsDomainEntityActor object
     */
    protected function _actionBlock(KCommandContext $context)
    {
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
        $this->getItem()->addBlocker($this->actor);
        
        return $this->getItem();
    }
    
    /**
     * The viewers unblocks the actor
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return ComActorsDomainEntityActor object
     */
    protected function _actionUnblock($context)
    {
        $this->getResponse()->status = KHttpResponse::RESET_CONTENT;
        
        $this->getItem()->removeBlocker($this->actor);    
        
        return $this->getItem();
    }        
    
    /**
     * Read Owner's Socialgraph
     * 
     * @param KCommandContext $context
     * 
     * @return AnDomainEntitysetDefault
     */
    protected function _actionGetgraph(KCommandContext $context)
    {            
        $this->getState()->insert('type','followers');        
        
        $filters  = array();
        $entities = array();
        $entity = $this->getItem();
        $viewer = get_viewer();
        
        if($this->getItem()->isFollowable())
        {
            if($this->type == 'followers') 
            {
                $entities = $this->getItem()->followers;
            } 
            elseif($this->type == 'blockeds' && $entity->authorize('administration')) 
            {
                $entities = $this->getItem()->blockeds;
            }
            elseif($this->type == 'leadables')
            {
            	if(!$entity->authorize('leadable'))
            	{
            		throw new LibBaseControllerExceptionForbidden('Forbidden');

            		return false;
            	}	
            	
            	$excludeIds = KConfig::unbox($entity->followers->id);
            	$excludeIds = array_merge($excludeIds, KConfig::unbox($entity->blockeds->id));
            	
            	if($viewer->admin())
            	{
            		$entities = $this->_mixer->getService('com://site/people.domain.entity.person')
            					->getRepository()->getQuery()
            					->where('person.id', 'NOT IN', $excludeIds);	
            	}
            	else 
            	{            	
            		$entities = $viewer->followers->where('actor.id', 'NOT IN', $excludeIds);
            	}
            	
            }
        }
        
        if($this->getItem()->isLeadable()) 
        {
            if($this->type == 'leaders') 
            {
                $entities = $this->getItem()->leaders;
            } 
            elseif($this->type == 'mutuals')
            {
                $entities = $this->getItem()->getMutuals();
            }
            elseif($this->type == 'commonleaders') 
            {             
                $entities = $this->getItem()->getCommonLeaders(get_viewer());
            }
        }
        
        if(!$entities)
            return false;
            
        $xid = (array) KConfig::unbox($this->getState()->xid);
        
        if(!empty($xid))
            $entities->where('id','NOT IN', $xid);
            
        $entities->limit($this->limit, $this->start);
            
        if($this->q)
            $entities->keyword($this->q);    
            
        $this->setList($entities->fetchSet())->actor($this->getItem());
       
        return $entities;
    }
            
    /**
     * Set the subejct before perform graph actions
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return ComActorsDomainEntityActor object
     */
    public function getActor(KCommandContext $context)
    {
        $data = $context->data;
        
        if($data->actor)
        {         
            $ret = $this->getService('repos:actors.actor')->fetch($data->actor);        
        }
        else
        { 
            $ret = get_viewer();
        }
       
        $this->actor = $ret;
       
        return $this->actor;
    }
}
