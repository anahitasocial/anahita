<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Package Controller
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerPackage extends ComBaseControllerService
{
    /*
     * an actor object that will subscribe to this package
     */      
    protected $_subscriber = null;
           
    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct( KConfig $config )
    {
        parent::__construct( $config );
        
        $this->registerCallback( array( 'before.edit', 'after.add' ), array( $this, 'setMeta' ) );
        
        $this->registerCallback( array(
                'before.editsubscription',
                'before.addsubscriber', 
                'before.deletesubscriber'), 
                array( $this, 'fetchSubscriber' ) );
                
        $this->registerCallback( array(
                'before.editsubscription',
                'before.addsubscriber'),  
                array( $this, 'setEndDate' ) );
    }
        
    /**
     * Read a package
     * 
     * @param KCommandContext $context
     * @return void
     */ 
    protected function _actionRead( KCommandContext $context )
    {
        $this->plugins = JPluginHelper::getPlugin( 'subscriptions' );  
              
        return parent::_actionRead( $context );
    }   
    
    /**
     * reassign subscriber to a different package
     * 
     * @param KCommandContext $context
     * @return void
     */
    protected function _actionEditsubscription( KCommandContext $context )
    {
        if( $subscription = $this->_subscriber->changeSubscriptionTo( $this->getItem() ) )
        {
           $subscription->endDate = $context->data->endDate;
        }
    }
    
    /**
     * Add a subscriber
     * 
     * @param KCommandContext $context
     * @return void
     */ 
    protected function _actionAddsubscriber( KCommandContext $context )
    {            
        if( $subscription = $this->_subscriber->subscribeTo( $this->getItem() ) )
        {
            $subscription->endDate = $context->data->endDate;
        }
    }
    
    /**
     * remove a subscriber
     * 
     * @param KCommandContext $context
     * @return void
     */ 
    protected function _actionDeletesubscriber( KCommandContext $context )
    {
       $this->_subscriber->subscription->delete();
    }
    
    /**
     * Set the entity gid
     * 
     * @param KCommandContext $context
     * @return boolean
     */
    public function setMeta( KCommandContext $context )
    {
        $data = $context->data;
        
        $entity = $this->getItem();
        
        $plugins = KConfig::unbox( pick( $data->plugins, array() ) );
        
        $entity->setPluginsValues( $plugins );
    }   
    
    /**
     * Fetches an entity
     *
     * @param KCommandContext $context
     * 
     * @return ComActorsDomainEntityActor
     */
    public function fetchSubscriber( KCommandContext $context )
    {
        if( !$this->_subscriber )
        {   
            $actor_id = $context->data->actor_id;
            
            $this->_subscriber = $this->getService('repos://site/actors.actor')->fetch( $actor_id );
            
            if( !$this->_subscriber ) 
            {
                throw new LibBaseControllerExceptionNotFound('Subscriber Not Found');
            }
        }                      
        
        return $this->_subscriber;
    }
    
    /**
     * Fetches an entity
     * 
     * @return null
     * @param object POST data
     */ 
    public function fetchEntity( KCommandContext $context )
    {
        if( in_array( $context->action, array( 'editsubscription', 'addsubscriber', 'deletesubscriber' ) ) )
        {       
            if( $context->data->package_id )
            {
               $this->id = $context->data->package_id;
            }    
        }
         
        return $this->__call( 'fetchEntity', array( $context ) );
    }
    
    /**
     * Set End Time action
     * 
     * sets correct end date value to be used for a subscription setting 
     *
     * @param KCommandContext $context Context parameter
     * 
     * @param void
     */
    public function setEndDate( KCommandContext $context )
    {
       $data = $context->data;
       
       if( $data->day || $data->month || $data->year )
       {    
           $date = new KDate();
       
           $date->day( (int) $data->day );
           
           $date->month( (int) $data->month );
           
           $date->year( (int) $data->year );
           
           $data->endDate = $date;
       }
    }
}