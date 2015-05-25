<?php 
/**
 * @version     $Id$
 * @category	Com_Subscriptions
 * @package		Controller
 * @copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://www.GetAnahita.com
 */

/**
 * Package Controller
 * 
 * @package	Com_Subscriptions
 * @category		Controller
 */
class ComSubscriptionsControllerOrder extends ComBaseControllerService
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
            'behaviors' => array( 
                'ownable',
                'serviceable' => array ( 
                    'read_only' => true
                    )    
                )
        ));
    
        parent::_initialize( $config );
    }
    
	/** 
	 * Service Browse
	 * 
	 * @param KCommandContext $context
	 * 
	 * @return void
	 */
	protected function _actionBrowse( $context )
 	{
 	    $entities = parent::_actionBrowse( $context );

        if( isset($this->actor->id) )
        {
           $entities->actorId( $this->actor->id ); 
        }
        
        return $entities;
 	}
}