<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Topics Controller
 *
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTopicsControllerTopic extends ComMediumControllerDefault
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'request' => array(
                'sort' => null,               
            ),
            'behaviors' => array(
                'pinnable'
            )
        ));
        
        parent::_initialize($config);
    }
    
	/**
	 * Browse Topics
	 * 
	 * @param KCommandContext $context Context
	 * 
	 * @return void
	 */
	protected function _actionBrowse( $context )
	{	
		$topics = parent::_actionBrowse( $context );
			
		$topics->order('IF(@col(lastCommentTime) IS NULL, @col(creationTime), @col(lastCommentTime))', 'DESC');
	}
	
	/**
	 * When a topic is added, then create a notification
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	protected function _actionAdd($context)
	{
	    $entity = parent::_actionAdd($context);
	    
	    if( $entity->owner->isSubscribable() )
	    {
    	    $notification = $this->createNotification(array(

    	    	'name' => 'topic_add',
    	        'object' => $entity,
    	        'subscribers' => $entity->owner->subscriberIds->toArray()
    	    
    	    ))->setType('post', array('new_post'=>true));
	    }
	        
	    return $entity;
	}
}