<?php
/**
 * Description of Merchant_Billing_PaypalExpress
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */

require_once dirname(__FILE__) . "/paypal/PaypalCommon.php";
require_once dirname(__FILE__) . "/paypal/PaypalExpressResponse.php";
class Merchant_Billing_PaypalExpress extends Merchant_Billing_PaypalCommon {
  const TEST_REDIRECT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
  const LIVE_REDIRECT_URL = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';

  private $version  = '59.0';

  private $options = array();
  protected $post = array();

  private $token;
  private $payer_id;

  protected $default_currency = 'EUR';
  protected $supported_countries = array('US');
  protected $homepage_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=xpt/merchant/ExpressCheckoutIntro-outside';
  protected $display_name = 'PayPal Express Checkout';


  public function __construct( $options = array() ) {

    $this->required_options('login, password, signature', $options);

    $this->options = $options;

    if( isset($options['version'])) $this->version = $options['version'];
    if( isset($options['currency'])) $this->default_currency = $options['currency'];
  }

  /**
   * Authorize and Purchase actions
   *
   * @param number $amount  Total order amount
   * @param Array  $options
   *               token    token param from setup action
   *               payer_id payer_id param from setup action
   *
   * @return Merchant_Billing_Response
   */
  public function authorize($amount, $options = array() ) {
    return $this->do_action($amount, "Authorization", $options);
  }

  /**
   *
   * @param number $amount
   * @param array $options
   * 
   * @return Merchant_Billing_Response
   */
  public function purchase($amount, $options = array()) {
    
    $this->post = array_merge($this->post, array('METHOD' => 'DoExpressCheckoutPayment'));
  	
  	return $this->do_action($amount, "Sale", $options);
  }
  
  /**
   *
   * @param number                      $money
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array                       $options
   *
   * @return Merchant_Billing_Response
   */  
   public function recurring($amount, $options=array()) {

   	$this->required_options('unit, start_date, occurrences', $options);
   	
    $params = array(
        'METHOD' 			=> 'CreateRecurringPaymentsProfile',
        'BILLINGFREQUENCY'	=> $options['occurrences'],
    	'BILLINGPERIOD'		=> $options['unit'],
    	'PROFILESTARTDATE'	=> $options['start_date'],
    	'DESC'				=> isset($options['description']) ? $options['description'] : '',
    	'IPADDRESS'			=> isset($options['ip']) ? $options['ip'] : $_SERVER['REMOTE_ADDR']
    );

    $this->post = array_merge($this->post, $params);
    
    return $this->do_action($amount, "Sale", $options);
  }

  /**
   * Setup Authorize and Purchase actions
   *
   * @param number $money  Total order amount
   * @param array  $options
   *               currency           Valid currency code ex. 'EUR', 'USD'. See http://www.xe.com/iso4217.php for more
   *               return_url         Success url (url from  your site )
   *               cancel_return_url  Cancel url ( url from your site )
   *
   * @return Merchant_Billing_Response
   */
  public function setup_authorize($money, $options = array()) {
    return $this->setup($money, 'Authorization', $options);
  }

  /**
   *
   * @param number $money
   * @param array $options
   * 
   * @return Merchant_Billing_Response
   */
  public function setup_purchase($money, $options = array()) {
    return $this->setup($money, 'Sale', $options);
  }

  private function setup( $money, $action, $options = array() ) {

    $this->required_options('return_url, cancel_return_url', $options);

    $params = array(
        'METHOD' 			=> 'SetExpressCheckout',
        'AMT'           	=> $this->amount($money),
        'RETURNURL'     	=> $options['return_url'],
        'CANCELURL'     	=> $options['cancel_return_url'],
    	'L_BILLINGTYPE0'	=> isset($options['billing_type']) ? $options['billing_type'] : '',
    	'L_BILLINGAGREEMENTDESCRIPTION0' => isset($options['billing_agreement_description']) ? $options['billing_agreement_description'] : '' 
    );

    $this->post = array_merge($this->post, $params);

    Merchant_Logger::log("Commit Payment Action: $action, Paypal Method: SetExpressCheckout");

    return $this->commit( $action );
  }

  private function do_action ($money, $action, $options = array() ) {
    if ( !isset($options['token']) ) $options['token'] = $this->token;
    if ( !isset($options['payer_id']) ) $options['payer_id'] = $this->payer_id;

    $this->required_options('token, payer_id', $options);

    $params = array(
        'AMT'           => $this->amount($money),
        'TOKEN'         => $options['token'],
        'PAYERID'       => $options['payer_id']);

    $this->post = array_merge($this->post, $params);

    Merchant_Logger::log("Commit Payment Action: $action, Paypal Method: ".$this->post['METHOD']);

    return $this->commit($action );

  }

  /**
   *
   * @param string $token
   *
   * @return string url address to redirect
   */
  public function url_for_token($token) {
    $redirect_url = $this->is_test() ? self::TEST_REDIRECT_URL : self::LIVE_REDIRECT_URL;
    return $redirect_url . $token;
  }

  /**
   *
   * @param string $token
   * @param string $payer_id
   *
   * @return Merchant_Billing_Response
   */
  public function get_details_for($token, $payer_id) {

    $this->payer_id = urldecode($payer_id);
    $this->token    = urldecode($token);

    $params = array(
        'METHOD' => 'GetExpressCheckoutDetails',
        'TOKEN'  => $token
    );
    $this->post = array_merge($this->post, $params);

    Merchant_Logger::log("Commit Paypal Method: GetExpressCheckoutDetails");
    return $this->commit($this->urlize( $this->post ) );

  }

  /**
   *
   * Add final parameters to post data and
   * build $this->post to the format that your payment gateway understands
   *
   * @param string $action
   * @param array $parameters
   */
  protected function post_data($action) {
    $params = array(
        'PAYMENTACTION' => $action,
        'USER'          => $this->options['login'],
        'PWD'           => $this->options['password'],
        'VERSION'       => $this->version,
        'SIGNATURE'     => $this->options['signature'],
        'CURRENCYCODE'  => $this->default_currency);
    
    $this->post = array_merge($this->post, $params);

    return $this->urlize( $this->post );
  }

  /**
   *
   * @param boolean $success
   * @param string  $message
   * @param array   $response
   * @param array   $options
   * 
   * @return Merchant_Billing_PaypalExpressResponse 
   */
  protected function build_response($success, $message, $response, $options=array()){
    return new Merchant_Billing_PaypalExpressResponse($success, $message, $response,$options);
  }
  
}
?>
