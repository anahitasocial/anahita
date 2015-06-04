<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Actor View (Profile View)
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsViewSettingHtml extends ComBaseViewHtml
{
    protected function _layoutDefault()
    {
        $subscription = null;    
           
       if( $this->actor->hasSubscription(false) )
       {
          $subscription = $this->actor->subscription; 
       }  
       
       $this->set( array(
            'subscription' => $subscription
        ));   
    }    
        
    protected function _layoutEdit()
    {
        $selectedPackageId = 0;
        
        $packages = $this->getService('repos:subscriptions.package')->getQuery()->fetchSet()->order('ordering');    
        
        $endDate = new KDate();
        
        if( $this->actor->hasSubscription(false) )
        {
           $selectedPackageId = $this->actor->subscription->package->id;
           
           $config = new KConfig(array(
                'date' => $this->actor->subscription->endDate
           )); 
           
           $endDate = new KDate( $config );
        }
        
        $this->set( array(
            'packages' => $packages,
            'selectedPackageId' => $selectedPackageId,
            'endDate' => $endDate
        ));
    }    
}    
    