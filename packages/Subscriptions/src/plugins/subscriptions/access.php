<?php
/**
 * @version		$Id$
 * @category	Anahita_Apps
 * @package	 	Plugin
 * @subpackage  Subscriptions
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

/**
 * Subscription system plugins. Validates the viewer subscriptions
 * 
 * @category	Anahita_Apps
 * @package	 	Plugin
 * @subpackage  Subscriptions
 */
class PlgSubscriptionsAccess extends PlgKoowaDefault 
{
	/**	
	 * Access
	 * 
	 * @var array
	 */
	protected $_packages;
	
	/**
     * Constructor
     */
	function __construct($dispatcher, $config = array())
	{
		parent::__construct($dispatcher, $config);

		$this->_packages = KService::get('repos://site/subscriptions.package')->getQuery()->disableChain()->fetchSet();
        
        KService::get('anahita:domain.store.database')
            ->addEventSubscriber($this);	
	}
	
	/**
	 * Event Listener
	 *
	 * @param KEvent $event
	 */
	public function onBeforeDomainStoreFetch(KEvent $event)
	{
        if ( !$event->query->getRepository()
                ->getEventDispatcher()->isSubscribed($this) ) 
        {
            //only add if it's a node 
            if ( $event->query->getRepository()->isNode() ) {
                $event->query->getRepository()->addEventSubscriber($this);
            }
        }
	}
	
	/**
	 * String
	 * 
	 * @param KEvent $event
	 */
	public function onBeforeDomainQuerySelect(KEvent $event)
	{
	     $this->_buildQuery($event);   
	}
	
	/**
	 * Event Listener
	 *
	 * @param KEvent $event
	 */
	public function onAfterDomainRepositoryFetch(KEvent $event)
	{  
	    $viewer = get_viewer();
	    $query  = $event->query;
        
	    //if method is GET and reading actor.
	    //that means we are in the actor profile page
	    //redirect to the list of package if not allowed to see the actor
	    //not seeing actor means not subscribed to any packages
        
	    if ( $query->access_changed && $event->data ) 
	    {
	        $entity   = $event->data;
            if ( is_person($entity) ) {
                return;   
            }
            
	        $option   = KRequest::get('get.option','cmd');
	        $id       = KRequest::get('get.id','cmd');
	        if ( !$entity->authorize('access') && $entity->id == $id && $entity->component == $option )
	        {
	            JFactory::getLanguage()->load('com_subscriptions');
	            JFactory::getApplication()->redirect('index.php?option=com_subscriptions&view=packages', JText::_('COM-SUBSCRIPTIONS-ACCESS-PLG-NO-SUBS'));
	        }
	    }
	}	
	
	/**
	 * After subscription purchase
	 *
	 * @param  KEvent $event
	 * @return void
	 */
	public function onAfterExpire($event)
	{
	    $subscription = $event->subscription;	    
	    //unfollow from the groups
	    $access   = new KConfig($subscription->package->getPluginValues('access'));
	    $ids      = array();
	    if ( $access->limited_actor_ids ) {
	        $ids = explode(',', $access->limited_actor_ids);
	        $ids = array_unique(array_filter($ids));
	    }    	    
	    $actors   = KService::get('repos://site/actors')
	                ->getQuery(true)
	                ->id($ids)->fetchSet();
	    
	    foreach($actors as $actor) {	        	        
            $actor->removeFollower($subscription->person);
	    }
	        
	}	
	
	/**
	 * Query
	 *
	 * @param KEvent               $evnet  Original Event
	 * 
	 * @return void
	 */
	protected function _buildQuery(KEvent $event)
	{
	    $viewer       = get_viewer();
	    if ( $viewer->admin() )
	        return;
	    $query        = $event->query;
	    $conditions   = array();
	    $is_actor         = $query->getRepository()->entityInherits('ComActorsDomainEntityActor');
	    $is_ownable       = $query->getRepository()->hasBehavior('ownable');
	    $clause           = $query->clause();
	    
	    if ( !$is_ownable && !$is_actor )
	        return;

	    $subscription_package_id = 0;
	    $package_ids             = array();
	    if ( $viewer->hasSubscription() ) 
	    {
	        $subscription_package_id = $viewer->subscription->package->id;
	    }
	    
	    foreach($this->_packages as $package)
	    {
	        if ( !$package->enabled )
	            continue;
	    
	        $package_ids[] = $package->id;
	        $access        = new KConfig($package->getPluginValues('access'));
	        
	        if ( empty($access->limited_actor_ids) && empty($access->open_actor_ids) )
	            continue;
	    
	        $subscribed = (int)$viewer->subscribedTo($package);
	        
	        if ( $is_actor ) 
	        {
	            $col     = 'id';
	            $default = 'access';
    	        if ( $subscribed )
    	            $subscribed = 'access';
    	        else
    	            $subscribed = '"admin"';
	        }
	        else 
	        {
	            $default = '1';
	            $col     = 'owner.id';
	        } 
	        
	        if ( $access->limited_actor_ids )
	        {
	            $ids 	    = array_unique(explode(',', implode(',', (array)$access['limited_actor_ids'])));
	            $condition	= '@col('.$col.') IN (%s)';
	        }
	        else
	        {
	            $ids 	    = array_unique(explode(',', implode(',', (array)$access['open_actor_ids'])));
	            $condition	= '@col('.$col.') NOT IN (%s)';
	        }
	        
	        $condition      = sprintf($condition, implode($ids,','));	        
	        $conditions[]   = $condition;
	    }
	   
	    if ( empty($conditions) )
	        return;
        if ( $is_actor && ($event->mode == AnDomain::FETCH_VALUE || $event->mode == AnDomain::FETCH_VALUE_LIST) )
	        return;
	    
	    $true        = '1';
	    $false       = '0';
	    $conditions  = implode($conditions, ' OR ');
	    $subscribed  = "$subscription_package_id IN (".implode(',',$package_ids).")";
// 	    $subscribed  = (int)in_array($subscription_package_id, $package_ids);	    
	    if ( $is_actor )
	    {
	        if ( $event->mode & AnDomain::FETCH_ENTITY ) 
            {
                $query->accessChanged(true);
                $query->getRepository()->addEventListener('onAfterDomainRepositoryFetch', $this);
            }
	        $conditions  = "($conditions) AND !@instanceof(ComPeopleDomainEntityPerson)";
	        $true  = 'access';
	        $false = '@quote(admin)';
	    } else {	    	   
	        $conditions  = "($conditions) AND @col(owner.type) NOT LIKE @quote(com:people.domain.entity.person)";
	    }
	    $conditions  = "IF($conditions, IF($subscribed,$true,$false),$true)";
	    if ( $is_actor ) 
	    {
	       if ( isset($query->operation['type']) && 
	               $query->operation['type'] == AnDomainQuery::QUERY_SELECT_DEFAULT  
	               ) {
	           $query->select(array('access'=>$conditions));
	       }
	    } 
	    else 
	    {
	        $query->where($conditions);
	    }
	    	    
	}
}