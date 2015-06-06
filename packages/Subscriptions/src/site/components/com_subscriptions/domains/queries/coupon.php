<?php

/** 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Coupon Query Class
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainQueryCoupon extends AnDomainQueryDefault 
{
    /**
     * Build the search query
     * 
     * @return void
     */
    protected function _beforeQuerySelect()
    {
        $code  = $this->keyword;
        
        if( $code )
        {
           $this->where( 'coupon_tbl.code', 'LIKE', '%'.$code.'%' ); 
        }
    }
}    