<?php

/** 
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage View
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Default Subscriptions Setting View (Profile View)
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage View
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsViewCouponHtml extends ComBaseViewHtml
{
    /**
     * Prepare form layout
     * 
     * @return null
     */    
    protected function _layoutForm()
    {
        if( !$this->expiresOn )
        {
           $expiresOn = new KDate();
           
           $expiresOn->addMonths(1);    
                            
           $this->set( array(
                'expiresOn' => $expiresOn
            )); 
        }
    }    
    
    /**
     * Prepare edit layout
     * 
     * @return null
     */    
    protected function _layoutEdit()
    {        
        $config = new KConfig(array(
            'date' => $this->item->expiresOn
        )); 

        $this->set( array(
            'expiresOn' => new KDate( $config )
        ));
    }    
}    