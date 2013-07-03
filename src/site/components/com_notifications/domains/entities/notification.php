<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Domains
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Notification Entity.
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Domains
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsDomainEntityNotification extends ComBaseDomainEntityNode 
{
	/**
	 * Notification Status
	 */
	const STATUS_NOT_SENT = 0;	
	const STATUS_SENT	  = 1;
	
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
			'attributes' => array(
				'name' => array('required'=>true),
				'type' => array('column'=>'body'),
				'creationTime'		 => array('default'=>'date','column'=>'created_on'),
				'status'			 => array('default'=>self::STATUS_NOT_SENT),
				'subscriberIds' 	 => array('type'=>'set',   'default'=>'set','write'=>'private','required'=>true)
			),
		    'behaviors' => array(
		    	  'serializable' => array('serializer'=>'com://site/stories.domain.serializer.story'),
		          'dictionariable'      
            ),
			'relationships'	 => array(			    
				'object'	 => array('polymorphic'=>true, 'type_column'=>'story_object_type', 'child_column'=>'story_object_id'),
				'subject'    => array('required'=>true,'parent'=>'com:actors.domain.entity.actor', 'child_column'=>'story_subject_id'),
				'target'     => array('required'=>true,'parent'=>'com:actors.domain.entity.actor', 'child_column'=>'story_target_id'),
				'comment'    => array('parent'=>'com:base.domain.entity.comment', 'child_column'=>'story_comment_id')
			 )
		));
						
		parent::_initialize($config);
	}	

	/**
     * Initializes the options for an entity after being created
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _afterEntityInstantiate(KConfig $config)
	{
		$data = $config->data;
		
		$data->append(array(
			'subscribers' => array()			
		));
		
		if ( is($data->object,'ComBaseDomainEntityComment') ) {
		    $data->comment = $data->object;
		    unset($data->object);
		}
		
		if ( $data->comment ) 
		{
		    $data->object = $data->comment->parent;
		    $data->append(array(
		         'subscribers' => array($data->comment->author->id)
		    ));		    
		}
        
		//force the target to be the owner of the object
		//if the object is ownable
		if ( $data->object && $data->object->isOwnable() ) {
		    $data->target = $data->object->owner;
		}
		
		if ( $data->object ) 
        {
			if ( $data->object->isModifiable() && empty($data->comment)) 
			{
				$data->append(array(
					'subscribers' => array($data->object->author->id)
				));
			}
		}
        //if there are no objects, then there are no subscribers
        //in that case add the target as the notification subscriber 
        //if it's notifiable
        elseif ( $data->target ) 
        {
            if ( $data->target->isNotifiable() )
            {
                $data->append(array(
                    'subscribers' => array($data->target->id)
                ));                
            } 
            //if not notiable but administrable 
            //then add all the admins
            elseif ( $data->target->isAdministrable() ) 
            {
                $data->append(array(
                    'subscribers' => $data->target->administratorIds->toArray()
                ));                
            }
        }

		parent::_afterEntityInstantiate($config);
		
		if ( $config->data->subscribers ) {
			$this->setSubscribers( $config->data->subscribers );
		}
	}
	
	/**
	 * Sets the type of the notification. If an array of configuration is passed, it will
	 * store it as the notification configuration.
	 *
	 * @param string $type   The type of the notification	 
	 * @param array  $config An array of configuration for the notification
	 * 
	 * @return ComNotificationsDomainEntityNotification
	 */
	public function setType($type, $config = array())
	{
	    $this->set('type', $type);
	    foreach($config as $key => $value)
	        $this->setValue($key, $value);
	    return $this;
	}
	
	/**
	 * Set a list of notifications subscribers
	 * 
	 * @param array $subscribers An array of Ids or person objects
	 * 
	 * @return void
	 */
	public function setSubscribers($subscribers)
	{
		//flatten the array
		$subscribers = AnHelperArray::getValues( KConfig::unbox($subscribers) );
		$ids 	= array();
		foreach($subscribers as $subscriber) 
        {
			if ( is($subscriber, 'AnDomainEntityAbstract') ) 
            {
                 $ids[]  = $subscriber->id;
			}
			else $ids[] = $subscriber;
		}
		
		$ids = array_unique($ids);
		
		if ( count($ids) > 0 ) 
		{
			$this->set('subscriberIds',   AnDomainAttribute::getInstance('set')->setData($ids));			
		}
		else $this->delete();
		
		return $this;
	}
	
	/**
	 * Removes an array of people or ids from the list of subscribers
	 * 
	 * @param ComActorsDomainEntityActor|array $subscribers An array of people or ids
	 * 
	 * @return void
	 */
	public function removeSubscribers($subscribers)
	{
		$subscribers = KConfig::unbox($subscribers);
		
		if ( is($subscribers, 'AnDomainEntityAbstract') ) {
			$subscribers = array($subscribers);
		}
		else {
			$subscribers = (array) $subscribers;
		}
		
		$ids = $this->subscriberIds->toArray();

		foreach($subscribers as $subscriber) {
			$id = is($subscriber, 'AnDomainEntityAbstract') ? $subscriber->id : $subscriber;
			unset($ids[$id]);
		}
		
		$this->set('subscriberIds',   AnDomainAttribute::getInstance('set')->setData($ids));
					
		//if there are no more subscriber then delete the notification
		if ( empty($ids) )
			$this->delete();
		
		return $this;
	}
	
	/**
	 * Checks with a setting delegate of the notification whether to notify a person or not
	 *
	 * @param ComPeopleDomainEntityPerson         $person
	 * @param ComNotificationsDomainEntitySetting $setting
	 * 
	 * @return int
	 */
	public function shouldNotify($person, $setting)
	{
        //if a person is not notifiable then return false
        if ( !$person->isNotifiable() )
            return false;
                   
	    //check if the target allows access to the person
        if (  !$this->target->allows($person, 'access') ) 
        {
            //if person can't see the target
            //then remove any bonds between the two
            if ( $this->target->isFollowable() ) {
                //$this->target->removeFollower($person);
            }
            
            if ( $person->isFollowable() ) {
                //$person->removeFollower($this->target);   
            }
            
            if ( isset($this->object) && $this->object->isSubscribable() ) {
                //$this->object->removeSubscriber($person);   
            }
            
            return false;
        } 
        elseif ( isset($this->object) && $this->object->isPrivatable() ) 
        {
            if ( !$this->object->allows($person, 'access') ) 
            {
                if ( $this->object->isSubscribable() ) {
                //    $this->object->removeSubscriber($person);
                }
                
                return false;   
            }
        }
        
	    if ( $this->type ) 
	    {
	         $delegate = $this->getService('com://site/notifications.domain.delegate.setting.'.$this->type);
	         return $delegate->shouldNotify($person, $this, $setting);
	    }
	    else return ComNotificationsDomainDelegateSettingInterface::NOTIFY_WITH_EMAIL;;
	}	
}