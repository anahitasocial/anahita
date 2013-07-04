<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Notification Controller
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsControllerNotification extends ComBaseControllerService
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
    }
        
   /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(			
			'behaviors'	=> array('ownable', 'serviceable'=>array('except'=>array('add','edit'))),
            'request'   => array('oid'=>'viewer')
		));
	
		parent::_initialize($config);
	}
	
    /**
     * Return the count of new notifications
     * 
     * @return string
     */
    protected function _actionGetcount(KCommandContext $context)
    {
        $count = $this->actor->numOfNewNotifications();
        return $this->getView()->newNotifications($count)->display();
    }
    
	/**
	 * Return a set of notification objects
	 * 
	 * @param  KCommandContext $context Context parameter 
     * 
	 * @return AnDomainEntitysetDefault
	 */
	protected function _actionBrowse($context)
	{         
        $this->actor->resetNotifications();
              
        if ( $this->actor->eql(get_viewer()) ) 
            $title = JText::_('COM-NOTIFICATIONS-ACTORBAR-YOUR-NOTIFICATIONS');  
        else 
            $title = sprintf(JText::_('COM-NOTIFICATIONS-ACTORBAR-ACTOR-NOTIFICATIONS'), $this->actor->name);
        
        $this->getToolbar('actorbar')->setTitle($title);
                
		$context->query = $this->actor->getNotifications()->getQuery();
        
        $set = parent::_actionBrowse($context)->order('creationTime','DESC');
          
        if ( $this->getRequest()->get('layout') != 'popover' ) {
            $set->limit(0);
        }
        
        if ( $this->new ) {
        	$set->id( $this->actor->newNotificationIds->toArray() );
        }
        
        //only zero the notifications if the viewer is the same as the 
        //actor. prevents from admin zeroing others notifications
        if ( $set->count() > 0 && get_viewer()->eql($this->actor) ) 
        {
            //set the number of notification, since it's going to be 
            //reset by the time it gets to the mod_viewer 
            KService::setConfig('mod://site/viewer.html', array('data'=>array('num_notifications'=>$this->actor->numOfNewNotifications())));            
            $this->registerCallback('after.get', array($this->actor,'viewedNotifications'), $set->toArray());
        }
        
		return $set;
	}
	
	/**
	 * Fake deleting a notification by removing the owner from the notification owners
	 * 
	 * @param  KCommandContext $context Context parameter
     * 
	 * @return AnDomainEntityAbstract
	 */
	protected function _actionDelete($context)
	{
        $this->actor->removeNotification($this->getItem());
		return $this->getItem();
	}
    
    /**
     * Checks if this controller can be executed by the viewer
     * 
     * @param string $action The action being executed
     * 
     * @return boolean
     */
    public function canExecute($action)
    {       
        if ( !$this->actor )
            return false;
        
        if ( !$this->actor->isNotifiable() )
            return false;
        
        if ( $this->actor->authorize('access') === false ) {
            return false;
        }
        
        if ( $this->actor->authorize('administration') === false )
            return false;
            
        return parent::canExecute($action);    
    }
}