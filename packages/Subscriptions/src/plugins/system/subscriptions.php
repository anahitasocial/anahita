<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Plugins
 * @subpackage System
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.plugin.plugin');

/**
 * Subscription system plugins. Validates the viewer subscriptions
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Plugin
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgSystemSubscriptions extends JPlugin 
{	
	/**
	 * onAfterRender handler
	 * 
	 * @return void
	 */
	public function onAfterRoute()
	{
        $person = get_viewer();
        	
		if ( $person->admin() )
        {
            return;
        }	
		
		KService::get('repos://site/subscriptions.package');
        
        //why isn't this in the below if statement?
		JPluginHelper::importPlugin('subscriptions', null, true, KService::get('anahita:event.dispatcher'));
		
		//if subscribe then unsubsribe 
		if ( isset( $person->subscription ) && $person->subscription->getTimeLeft() < 0 ) 
		{
		    //JPluginHelper::importPlugin('subscriptions', null, true, KService::get('anahita:event.dispatcher'));
                
		    dispatch_plugin('subscriptions.onAfterExpire', array( 'subscription' => $person->subscription ) );
            
		    $person->subscription = null;
		    
		    if ( KService::get('anahita:domain.space')->commitEntities() )
		    {
		        //redirect
		        $url = (string) KRequest::url();
                
		        KService::get('application.dispatcher')->getResponse()->setRedirect( $url )->send();
		        
		        exit(0);		        
		    }
		}
        
	}	
}