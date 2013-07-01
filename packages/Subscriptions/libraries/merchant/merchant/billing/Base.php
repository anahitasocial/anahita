<?php
/**
 * Description of MerchantBase
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
#require_once dirname(__FILE__) . "/../common.php";
class Merchant_Billing_Base {

  public static $gateway_mode;

  public static $integration_mode;

  protected static $mode;

  public static function mode($mode) {
    self::$mode = $mode;
    self::$gateway_mode = $mode;
    self::$integration_mode = $mode;
  }

  public static function is_test() {
    return self::$gateway_mode == 'test';
  }

  public static function gateway($gateway=null, $options = array()) {
    $gateway = "Merchant_Billing_" . Inflect::camelize($gateway);
    return new $gateway($options);
  }

}
?>
