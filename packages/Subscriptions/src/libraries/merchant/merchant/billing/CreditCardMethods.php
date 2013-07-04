<?php
/**
 * Description of CreditCardMethods
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_CreditCardMethods {
  private static $CARD_COMPANIES = array(
        'visa'               => '/^4\d{12}(\d{3})?$/',
        'master'             => '/^(5[1-5]\d{4}|677189)\d{10}$/',
        'discover'           => '/^(6011|65\d{2})\d{12}$/',
        'american_express'   => '/^3[47]\d{13}$/',
        'diners_club'        => '/^3(0[0-5]|[68]\d)\d{11}$/',
        'jcb'                => '/^35(28|29|[3-8]\d)\d{12}$/',
        'switch'             => '/^6759\d{12}(\d{2,3})?$/',
        'solo'               => '/^6767\d{12}(\d{2,3})?$/',
        'dankort'            => '/^5019\d{12}$/',
        'maestro'            => '/^(5[06-8]|6\d)\d{10,17}$/',
        'forbrugsforeningen' => '/^600722\d{10}$/',
        'laser'              => '/^(6304|6706|6771|6709)\d{8}(\d{4}|\d{6,7})?$/');

  public static function valid_month($month){
    $month = (int) $month;
    return ($month >= 1 && $month <= 12);
  }

  public static function valid_expiry_year($year){
    $year_now = date("Y",time());
    return ($year >= $year_now && $year <= ($year_now + 20) );
  }

  public static function valid_start_year($year) {
    return (preg_match("/^\d{4}$/", $year) && $year > 1987);
  }

  public static function valid_issue_number($number) {
    return preg_match("/^\d{1,2}$/", $number);
  }

  public static function CARD_COMPANIES(){
    return self::$CARD_COMPANIES;
  }

  public static function valid_number($number) {
    return ( ( self::valid_card_number_length($number) &&
            self::valid_checksum($number) ) );
  }

  public static function type($number) {
    if ( self::valid_test_mode_card_number($number) ) return 'bogus';
    foreach ( self::$CARD_COMPANIES as $name => $pattern ) {
      if ( $name == 'maestro' ) continue;
      if ( preg_match($pattern, $number) )
              return $name;
    }
    if ( preg_match(self::$CARD_COMPANIES['maestro'], $number) )
      return 'maestro';
  }

  public static function get_last_digits($number) {
    return strlen($number) <= 4 ? $number : substr($number, -4);
  }

  public static function mask($number) {
    return "XXXX-XXXX-XXXX-" . self::last_digits($number);
  }

  public static function matching_type($number, $type) {
    return (self::type($number) == $type) ;
  }

  private static function valid_card_number_length($number){
    return ( strlen($number) >= 12 );
  }

  private static function valid_test_mode_card_number($number){
    return Merchant_Billing_Base::is_test() && in_array($number, array( '1','2','3','success','failure','error') );
  }

  /**
   * Checks the validity of a card number by use of the the Luhn Algorithm.
   * Please see http://en.wikipedia.org/wiki/Luhn_algorithm for details.
   */
  private static function valid_checksum($number){
   $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 2, 4, 6, 8, 1, 3, 5, 7, 9);
   $sum = 0;
   $last = strlen($number) - 1;
   for ($i = 0; $i <= $last; $i++) {
      $sum += $map[$number[$last - $i] + ($i & 1) * 10];
   }
   
   return ($sum % 10 == 0);
  }
}
?>
