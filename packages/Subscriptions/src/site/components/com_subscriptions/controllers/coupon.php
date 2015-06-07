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
 * Coupon Controller
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerCoupon extends ComBaseControllerService
{
    protected function _actionPost( KCommandContext $context )
    {
        $data = $context->data;
        
        $date = new KDate();
       
        $date->day( (int) $data->expiresOnDay );
       
        $date->month( (int) $data->expiresOnMonth );
       
        $date->year( (int) $data->expiresOnYear );
       
        $data->expiresOn = $date;    
            
        return parent::_actionPost( $context );
    }
}
    