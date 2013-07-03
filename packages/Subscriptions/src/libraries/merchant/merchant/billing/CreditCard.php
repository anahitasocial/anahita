<?php
/**
 * Description of CreditCard
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */

require_once dirname(__FILE__) . "/CreditCardMethods.php";
require_once dirname(__FILE__) . "/ExpiryDate.php";

class Merchant_Billing_CreditCard extends Merchant_Billing_CreditCardMethods {

  public $first_name;
  public $last_name;
  public $month;
  public $year;
  public $type;
  public $number;
  public $verification_value;

  # Required for Switch / Solo cards
  public $start_month;
  public $start_year;
  public $issue_number;

  private $errors;

  public $require_verification_value = true;

  public function __construct($options) {
    
    $this->first_name = $options['first_name'];
    $this->last_name  = $options['last_name'];
    $this->month      = $options['month'];
    $this->year       = $options['year'];
    $this->number     = $options['number'];
    
    if ( isset($options['verification_value']) )
      $this->verification_value = $options['verification_value'];

    if ( isset($options['start_month']) )
      $this->start_month = $options['start_month'];

    if ( isset($options['start_year']) )
      $this->start_year = $options['start_year'];

    if ( isset($options['issue_number']) )
      $this->issue_number = $options['issue_number'];

    if ( isset($options['type']) )
      $this->type = $options['type'];
    else
      $this->type = self::type($this->number);

    $this->errors = new Merchant_Error();
  }

  public function __get($name) {
    if ( isset($this->$name) ) return $this->$name;
  }

  public function errors() {
    return $this->errors->errors();
  }
  public function expire_date(){
    return new Merchant_Billing_ExpiryDate($this->month, $this->year);
  }

  public function is_expired () {
    return $this->expire_date()->is_expired();
  }

  public function name() {
    return $this->first_name . " " . $this->last_name;
  }

  public function display_number() {
    return self::mask($this->number);
  }

  public function last_digits() {
    return self::get_last_digits($this->number);
  }

  public function is_valid() {
    $this->validate();
    $errors = $this->errors();
    return empty($errors);
  }

  private function validate() {
    $this->validate_essential_attributes();

    # Skip test if gateway is Bogus
    if ( self::type($this->number) == 'bogus' ) return true;

    $this->validate_card_type();
    $this->validate_card_number();
    $this->validate_verification_value();
    $this->validate_switch_or_solo_attributes();
  }


  private function validate_card_number() {
    if ( self::valid_number($this->number) === false )
            $this->errors->add('number', 'is not a valid credit card number');
    if ( self::matching_type($this->number, $this->type) === false )
            $this->errors->add('type', 'is not the correct card type');
  }

  private function validate_card_type() {
    if ( $this->type === null || $this->type == "")
            $this->errors->add('type', 'is required');
    $card_companies = self::card_companies();
    if ( !isset( $card_companies[$this->type] ) )
            $this->errors->add('type', 'is invalid');
  }

  private function validate_verification_value() {
    if ( $this->require_verification_value === true ) {
      if ( $this->verification_value === null || $this->verification_value == "" )
              $this->errors->add('verification_value', 'is required');
    }
  }

  private function validate_switch_or_solo_attributes() {
    if ( in_array( $this->type, array('solo', 'switch') ) ) {
      if ( ( self::valid_month($this->start_month) === false
              && self::valid_start_year($this->start_year) == false )
              || self::valid_issue_number($this->issue_number) ==false ) {

        if ( self::valid_month($this->start_month) === false )
                $this->errors->add('start month', 'is invalid');
        if ( self::valid_month($this->start_year) === false )
                $this->errors->add('start year', 'is invalid');
        if ( self::valid_issue_number($this->issue_number) === false )
                $this->errors->add('issue number', 'cannot be empty');
      }
    }
  }

  private function validate_essential_attributes() {
    if ( $this->first_name === null || $this->first_name == "")
            $this->errors->add('first_name', 'cannot be empty');
    if ( $this->last_name === null || $this->last_name == "")
            $this->errors->add('last_name', 'cannot be empty');
    if ( self::valid_month($this->month) === false )
            $this->errors->add('month', 'is not a valid month');
    if ( $this->is_expired() === true )
            $this->errors->add('year', 'expired');
    if ( self::valid_expiry_year($this->year) === false )
            $this->errors->add('year', 'is not a valid year');
  }


}
?>
