<?php

/** 
 * LICENSE: ##LICENSE##
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
 * Subscriptions Setting Contorller
 * 
 * This is not a dispatchable controller, but it's called as HMVC from an actor 
 * setting page
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerSetting extends ComBaseControllerResource
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
                'ownable'          
            )
        ));
    
        parent::_initialize($config);
    }
	
	/**
	 * Read action
	 * 
	 * Renders the actor setting for package subscription
	 *
	 * @param KCommandContext $context Context parameter
	 * 
	 * @param void
	 */
	protected function _actionRead(KCommandContext $context)
	{
        $this->getService('repos://site/subscriptions.package');       		
        $this->setItem( $this->actor->subscription );
	}
}
