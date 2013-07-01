<?php

/** 
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller_Validator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Coupon Validator
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Controller_Validator
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsControllerValidatorCoupon extends LibBaseControllerValidatorDefault
{
    /**
     * Validates a coupon code
     *
     * @param string $code    The coupon code to validate
     *
     * @return boolean
     */
    public function validateCode($code)
    {
        $coupon = $this->_controller->getRepository()->find(array('code'=>$code));
		
		if ( !$coupon ) {
		    $this->setMessage(JText::_('COM-SUB-INVALID-COUPON'));
			return false;
		}
		else 
            $this->setMessage(array('discount'=>$coupon->discount));
    }
}