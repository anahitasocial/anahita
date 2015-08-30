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
 * @link       http://www.GetAnahita.com
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
 * @link       http://www.GetAnahita.com
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
        
		//if subscribe then unsubsribe 
		if( isset( $person->subscription ) && $person->subscription->getTimeLeft() < 0 ) 
		{
            $person->unsubscribe();
            
            $url = JRoute::_( 'index.php?option=com_subscriptions&view=packages' );
            
            KService::get('application.dispatcher')->getResponse()->setRedirect( $url )->send();
		}
        
        return;
	}	
}