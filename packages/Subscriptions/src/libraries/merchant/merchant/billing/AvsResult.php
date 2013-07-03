<?php
/**
 * Description of AvsResult
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 * Implements the Address Verification System
 * https://www.wellsfargo.com/downloads/pdf/biz/merchant/visa_avs.pdf
 * http://en.wikipedia.org/wiki/Address_Verification_System
 * http://apps.cybersource.com/library/documentation/dev_guides/CC_Svcs_IG/html/app_avs_cvn_codes.htm#app_AVS_CVN_codes_7891_48375
 * http://imgserver.skipjack.com/imgServer/5293710/AVS%20and%20CVV2.pdf
 * http://www.emsecommerce.net/avs_cvv2_response_codes.htm
 */
class Merchant_Billing_AvsResult {

  private static $MESSAGES = array(
  'A' => 'Street address matches, but 5-digit and 9-digit postal code do not match.',
  'B' => 'Street address matches, but postal code not verified.',
  'C' => 'Street address and postal code do not match.',
  'D' => 'Street address and postal code match.',
  'E' => 'AVS data is invalid or AVS is not allowed for this card type.',
  'F' => 'Card member\'s name does not match, but billing postal code matches.',
  'G' => 'Non-U.S. issuing bank does not support AVS.',
  'H' => 'Card member\'s name does not match. Street address and postal code match.',
  'I' => 'Address not verified.',
  'J' => 'Card member\'s name, billing address, and postal code match. Shipping information verified and chargeback protection guaranteed through the Fraud Protection Program.',
  'K' => 'Card member\'s name matches but billing address and billing postal code do not match.',
  'L' => 'Card member\'s name and billing postal code match, but billing address does not match.',
  'M' => 'Street address and postal code match.',
  'N' => 'Street address and postal code do not match.',
  'O' => 'Card member\'s name and billing address match, but billing postal code does not match.',
  'P' => 'Postal code matches, but street address not verified.',
  'Q' => 'Card member\'s name, billing address, and postal code match. Shipping information verified but chargeback protection not guaranteed.',
  'R' => 'System unavailable.',
  'S' => 'U.S.-issuing bank does not support AVS.',
  'T' => 'Card member\'s name does not match, but street address matches.',
  'U' => 'Address information unavailable.',
  'V' => 'Card member\'s name, billing address, and billing postal code match.',
  'W' => 'Street address does not match, but 9-digit postal code matches.',
  'X' => 'Street address and 9-digit postal code match.',
  'Y' => 'Street address and 5-digit postal code match.',
  'Z' => 'Street address does not match, but 5-digit postal code matches.'
  );

  # Map vendor's AVS result code to a postal match code
  private $POSTAL_MATCH_CODE = array(
  'Y' => array( 'D', 'H', 'F', 'H', 'J', 'L', 'M', 'P', 'Q', 'V', 'W', 'X', 'Y', 'Z' ),
  'N' => array( 'A', 'C', 'K', 'N', 'O' ),
  'X' => array( 'G', 'S' ),
  null => array( 'B', 'E', 'I', 'R', 'T', 'U' )
  );

  # Map vendor's AVS result code to a street match code
  public $STREET_MATCH_CODE = array(
  'Y' => array( 'A', 'B', 'D', 'H', 'J', 'M', 'O', 'Q', 'T', 'V', 'X', 'Y' ),
  'N' => array( 'C', 'K', 'L', 'N', 'P', 'W', 'Z' ),
  'X' => array( 'G', 'S' ),
  null => array( 'E', 'F', 'I', 'R', 'U' )
  );

  protected $code = 'X';
  protected $message;
  protected $street_match;
  protected $postal_match;

  public static function messages() {
    return self::$MESSAGES;
  }

  public function  __construct($attr) {
    $street_match_code = array();
    $postal_match_code = array();
    
    foreach ($this->STREET_MATCH_CODE as $k => $v) {
      foreach ( $v as $l ) {
        $street_match_code[$l] = $k;
      }
    }
    
    $this->STREET_MATCH_CODE = $street_match_code;
    
    foreach ($this->POSTAL_MATCH_CODE as $k => $v) {
      foreach ( $v as $l ) {
        $postal_match_code[$l] = $k;
      }
    }
    $this->POSTAL_MATCH_CODE = $postal_match_code;

    if ( null === $attr ) $attr = array();

    if ( isset( $attr['code'] ) && !empty($attr['code']) && $attr['code']!='null' )
      $this->code = strtoupper($attr['code']);
    $this->message = self::$MESSAGES[$this->code];

    if ( !isset($attr['street_match']) ) {
      $this->street_match = $this->STREET_MATCH_CODE[$this->code];
    } else {
      $this->street_match = strtoupper($attr['street_match']);
    }

    if ( !isset($attr['postal_match']) ) {
      $this->postal_match = $this->POSTAL_MATCH_CODE[$this->code];
    } else {
      $this->postal_match = strtoupper($attr['postal_match']);
    }

  }
  

  public function toArray() {
    return array(
    'code' => $this->code,
    'message' => $this->message,
    'street_match' => $this->street_match,
    'postal_match' => $this->postal_match
    );
  }
}
?>
