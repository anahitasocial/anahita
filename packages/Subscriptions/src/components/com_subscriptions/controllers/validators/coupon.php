<?php

/**
 * Coupon Validator.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerValidatorCoupon extends LibBaseControllerValidatorDefault
{
    /**
     * Validates a coupon code.
     *
     * @param string $code The coupon code to validate
     *
     * @return bool
     */
    public function validateCode($code)
    {
        $coupon = $this->_controller->getRepository()->find(array('code' => $code));

        if ($coupon && !$coupon->expired()) {
            $discount = $coupon->discount * 100;
            $this->setMessage(AnTranslator::sprintf('COM-SUBSCRIPTIONS-VALID-COUPON', $discount));
            return true;
        } else {
            $this->setMessage(AnTranslator::_('COM-SUBSCRIPTIONS-INVALID-COUPON'));
            return false;
        }
    }
}
