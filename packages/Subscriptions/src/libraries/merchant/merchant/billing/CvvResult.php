<?php
/**
 * Description of CvvResult
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_CvvResult {
  private static $MESSAGES = array(
  'D'  =>  'Suspicious transaction',
  'I'  =>  'Failed data validation check',
  'M'  =>  'Match',
  'N'  =>  'No Match',
  'P'  =>  'Not Processed',
  'S'  =>  'Should have been present',
  'U'  =>  'Issuer unable to process request',
  'X'  =>  'Card does not support verification'
  );

  protected $code = 'X';
  protected $message;

  public function  __construct($code) {
    if ( $code != "" )
      $this->code = $code;

    $this->message = self::$MESSAGES[$this->code];
  }

  public static function messages(){
    return self::$MESSAGES;
  }

  public function toArray() {
    return array(
      'code' => $this->code,
      'message' => $this->message
    );
  }
}
?>
