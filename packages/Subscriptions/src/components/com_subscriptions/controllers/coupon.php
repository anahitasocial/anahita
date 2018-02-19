<?php

/**
 * Coupon Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerCoupon extends ComBaseControllerService
{
    protected function _actionPost(KCommandContext $context)
    {
        $data = $context->data;
        $date = new AnDate();
        $date->day((int) $data->expiresOnDay);
        $date->month((int) $data->expiresOnMonth);
        $date->year((int) $data->expiresOnYear);
        $data->expiresOn = $date;

        return parent::_actionPost($context);
    }
}
