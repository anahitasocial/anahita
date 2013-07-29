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
 * @package    Anahita_Plugins
 * @subpackage System
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class plgSystemSubscriptions extends JPlugin 
{	
	/**
	 * onAfterRender handler
	 * 
	 * @return void
	 */
	public function onAfterRoute()
	{
		global $mainframe;
		
		if ( $mainframe->isAdmin() ) return;
		
		JPluginHelper::importPlugin('system','anahita');
		
        
		JPluginHelper::importPlugin('subscriptions', null, true);
		
		$viewer = get_viewer();
		
		KService::get('repos://site/subscriptions.package');
		
		if ( $viewer->hasSubscription() ) 
		{
			$renewTime = get_config_value('subscriptions.renew_notice', 5);
			
			JFactory::getLanguage()->load('com_subscriptions');
			$subscription = $viewer->subscription;
			$url 	  = JRoute::_('index.php?option=com_subscriptions&view=packages');			
			$timeleft = ceil(AnHelperDate::secondsTo('day', $viewer->subscription->getTimeLeft()));
			
			if ( $timeleft > $renewTime )
				return;
			elseif ( $timeleft > 0 ) {
				$message  = sprintf(JText::_('AN-SB-PACKAGE-ABOUT-TO-EXPIRE'),$subscription->package->name, $timeleft.' '.JText::_('Days'), $url);				
			} elseif ( $timeleft < 0 ) {
				$message  = sprintf(JText::_('AN-SB-PACKAGE-HAS-EXPIRED'),$subscription->->package->name, $url);
			}
			
			if ( empty($message) )
			        return;
			$message  = '<div class="alert-message warning"><p>'.$message.'</p></div>';
			$message  = '<module position="maintop-a">'.$message.'</module>';
			
			JFactory::getDocument()->setBuffer($message,'component','subscription-notice');
		}
	}
	
}