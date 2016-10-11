<?php

/**
 * Coupon Query Class.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2015 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainQueryCoupon extends AnDomainQueryDefault
{
    /**
     * Build the search query.
     */
    protected function _beforeQuerySelect()
    {
        $code = $this->keyword;

        if ($code) {
            $this->where('coupon_tbl.code', 'LIKE', '%'.$code.'%');
        }
    }
}
