<?php
/**
 * Description of Merchant_Billing_AuthorizeNet
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_AuthorizeNet extends Merchant_Billing_Gateway {
  const API_VERSION = "3.1";

  const TEST_URL = "https://test.authorize.net/gateway/transact.dll";
  const LIVE_URL = "https://secure.authorize.net/gateway/transact.dll";
  const ARB_TEST_URL = 'https://apitest.authorize.net/xml/v1/request.api';
  const ARB_LIVE_URL = 'https://api.authorize.net/xml/v1/request.api';

  public $duplicate_window;

  const APPROVED     = 1;
  const DECLINED     = 2;
  const ERROR        = 3;
  const FRAUD_REVIEW = 4;

  const RESPONSE_CODE           = 0;
  const RESPONSE_REASON_CODE    = 2;
  const RESPONSE_REASON_TEXT    = 3;
  const AVS_RESULT_CODE         = 5;
  const TRANSACTION_ID          = 6;
  const CARD_CODE_RESPONSE_CODE = 38;

  protected $supported_countries = array('US');
  protected $supported_cardtypes  = array('visa', 'master', 'american_express', 'discover');
  protected $homepage_url = 'http://www.authorize.net/';
  protected $display_name = 'Authorize.Net';

  private $post = array();
  private $xml;
  private $options = array();
  private $CARD_CODE_ERRORS = array( 'N', 'S' );
  private $AVS_ERRORS = array( 'A', 'E', 'N', 'R', 'W', 'Z' );

  private $AUTHORIZE_NET_ARB_NAMESPACE = 'AnetApi/xml/v1/schema/AnetApiSchema.xsd';

  private $RECURRING_ACTIONS = array(
    'create' => 'ARBCreateSubscriptionRequest',
    'update' => 'ARBUpdateSubscriptionRequest',
    'cancel' => 'ARBCancelSubscriptionRequest'
  );

  public function  __construct($options) {
    $this->required_options('login, password', $options);

    $this->options = $options;
  }
  /**
   *
   * @param number                      $money
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array                       $options
   *
   * @return Merchant_Billing_Response
   */
  public function authorize($money, Merchant_Billing_CreditCard $creditcard, $options = array()) {
    $this->add_invoice($options);
    $this->add_creditcard($creditcard);
    $this->add_address($options);
    $this->add_customer_data($options);
    $this->add_duplicate_window();

    return $this->commit('AUTH_ONLY', $money);
  }

  /**
   *
   * @param number                      $money
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array                       $options
   *
   * @return Merchant_Billing_Response
   */
  public function purchase($money, Merchant_Billing_CreditCard $creditcard, $options = array()) {

    $this->add_invoice($options);
    $this->add_creditcard($creditcard);
    $this->add_address($options);
    $this->add_customer_data($options);
    $this->add_duplicate_window();

    return $this->commit('AUTH_CAPTURE', $money);
  }

  /**
   *
   * @param number $money
   * @param string $authorization
   * @param array  $options
   *
   * @return Merchant_Billing_Response
   */
  public function capture($money, $authorization, $options = array()) {
    $this->post = array('trans_id' => $authorization);
    $this->add_customer_data($options);
    return $this->commit('PRIOR_AUTH_CAPTURE', $money);
  }

  /**
   *
   * @param string $authorization
   * @param array  $options
   *
   * @return Merchant_Billing_Response
   */

  public function void($authorization, $options = array()) {
    $this->post = array('trans_id' => $authorization);
    return $this->commit('VOID', null);
  }


  /**
   *
   * @param number $money
   * @param string $identification
   * @param array  $options
   *
   * @return Merchant_Billing_Response
   */
  public function credit($money, $identification, $options = array()) {
     $this->required_options('card_number', $options);
     $this->post = array(
         'trans_id' => $identification,
         'card_num' => $options['card_number']
     );


     $this->add_invoice($options);
     return $this->commit('CREDIT', $money);
  }


  /**
   *
   * @param number                      $money
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array                       $options
   */
  public function recurring($money, Merchant_Billing_CreditCard $creditcard, $options=array()) {
    $this->required_options('length, unit, start_date, occurrences, billing_address', $options);
    $this->required_options('first_name, last_name', $options['billing_address']);

    $amount = $this->amount($money);

    $ref_id = isset($parameters['order_id']) ? $parameters['order_id'] : " ";
    $this->xml = "<refId>$ref_id</refId>";
    $this->xml .= "<subscription>";
    $this->arb_add_subscription($amount, $options);
    $this->arb_add_creditcard($creditcard);
    $this->arb_add_address($options['billing_address']);
    $this->xml .= "</subscription>";
    return $this->recurring_commit('create');
  }


  /**
   *
   * @param string                      $subscription_id subscription id returned from recurring method
   * @param Merchant_Billing_CreditCard $creditcard
   *
   * @return Merchant_Billing_Response
   */
  public function update_recurring($subscription_id, Merchant_Billing_CreditCard $creditcard) {

    $this->xml = <<<XML
            <subscriptionId>$subscription_id</subscriptionId>
              <subscription>
XML;
    $this->arb_add_creditcard($creditcard);
    $this->xml .= "</subscription>";

    return $this->recurring_commit('update');
  }

  /**
   *
   * @param string $subscription_id subscription id return from recurring method
   *
   * @return Merchant_Billing_Response
   */
  public function cancel_recurring($subscription_id) {

    $this->xml = "<subscriptionId>$subscription_id</subscriptionId>";

    return $this->recurring_commit('cancel');
  }

  /* Private */

  /**
   *
   * @param string $action
   * @param number $money
   * @param array  $parameters
   *
   * @return Merchant_Billing_Response
   */
  private function commit($action, $money, $parameters = array()) {
    if ($action != 'VOID')
      $parameters['amount'] = $this->amount($money);

    /*Request a test response*/
    # $parameters['test_request'] = $this->is_test() ? 'TRUE' : 'FALSE';

    $url = $this->is_test() ? self::TEST_URL : self::LIVE_URL;

    $data = $this->ssl_post($url, $this->post_data($action, $parameters));

    $response = $this->parse($data);

    $message = $this->message_from($response);

    $test_mode = $this->is_test();

    return new Merchant_Billing_Response(
            $this->success_from($response),
            $message,
            $response,
            array(
              'test' => $test_mode,
              'authorization' => $response['transaction_id'],
              'fraud_review' => $this->fraud_review_from($response),
              'avs_result' => array( 'code' => $response['avs_result_code'] ),
              'cvv_result' => $response['card_code']
            )
    );
  }

  /**
   *
   * @param string $response
   *
   * @return bool
   */
  private function success_from($response) {
    return $response['response_code'] == self::APPROVED;
  }

  /**
   *
   * @param string $response
   *
   * @return bool
   */
  private function fraud_review_from($response) {
    return $response['response_code'] == self::FRAUD_REVIEW;
  }

  /**
   *
   * @param string $response
   *
   * @return string
   */
  private function message_from($response) {
    if ( $response['response_code'] == self::DECLINED ) {
      if ( in_array( $response['card_code'], self::$CARD_CODE_ERRORS ) ) {
        $cvv_messages = Merchant_Billing_CvvResult::messages();
        return $cvv_messages[$response['card_code']];
      }
      if ( in_array( $response['avs_result_code'], self::$AVS_ERRORS ) ) {
        $avs_messages = Merchant_Billing_AvsResult::messages();
        return $avs_messages[$response['avs_result_code']];
      }
    }

    return $response['response_reason_text'] === null ? '' : $response['response_reason_text'];
  }

  /**
   *
   * @param string $body raw gateway response
   *
   * @return array gateway response in array format.
   */
  private function parse($body) {
    $fields = explode('|', $body);
    $response = array(
      'response_code' => $fields[self::RESPONSE_CODE],
      'response_reason_code' => $fields[self::RESPONSE_REASON_CODE],
      'response_reason_text' => $fields[self::RESPONSE_REASON_TEXT],
      'avs_result_code' => $fields[self::AVS_RESULT_CODE],
      'transaction_id' => $fields[self::TRANSACTION_ID],
      'card_code' => $fields[self::CARD_CODE_RESPONSE_CODE]
    );

    return $response;
  }

  private function post_data($action, $parameters = array()) {

    $this->post['version']        = self::API_VERSION;
    $this->post['login']          = $this->options['login'];
    $this->post['tran_key']       = $this->options['password'];
    $this->post['relay_response'] = 'FALSE';
    $this->post['type']           = $action;
    $this->post['delim_data']     = 'TRUE';
    $this->post['delim_char']     = '|';

    $this->post = array_merge($this->post, $parameters);
    $request = "";

    #Add x_ prefix to all keys
    foreach ( $this->post as $k=>$v ) {
      $request .= 'x_' . $k . '=' . urlencode($v).'&';
    }
    return rtrim($request,'& ');
  }

  private function add_invoice($options) {
    $this->post['invoice_num'] = isset($options['order_id']) ? $options['order_id'] : null;
    $this->post['description'] = isset($options['description']) ? $options['description'] : null;
  }

  private function add_creditcard(Merchant_Billing_CreditCard $creditcard) {
    $this->post['method']     = 'CC';
    $this->post['card_num']   = $creditcard->number;
    if ( $creditcard->require_verification_value )
      $this->post['card_code']  = $creditcard->verification_value;
    $this->post['exp_date']   = $this->expdate($creditcard);
    $this->post['first_name'] = $creditcard->first_name;
    $this->post['last_name']  = $creditcard->last_name;
  }

  private function expdate(Merchant_Billing_CreditCard $creditcard) {
    $year  = $this->cc_format($creditcard->year, 'two_digits');
    $month = $this->cc_format($creditcard->month, 'two_digits');
    return  $month . $year;
  }

  private function add_address($options) {
    $address = isset($options['billing_address']) ? $options['billing_address'] : $options['address'];
    $this->post['address'] = isset($address['address1'])? $address['address1'] : null;
    $this->post['company'] = isset($address['company']) ? $address['company'] : null;
    $this->post['phone']   = isset($address['phone'])   ? $address['phone']   : null;
    $this->post['zip']     = isset($address['zip'])     ? $address['zip']     : null;
    $this->post['city']    = isset($address['city'])    ? $address['city']    : null;
    $this->post['country'] = isset($address['country']) ? $address['country'] : null;
    $this->post['state']   = isset($address['state'])   ? $address['state']   : 'n/a';
  }

  private function add_customer_data($options) {
    if ( isset($options['email']) ) {
      $this->post['email'] = isset( $options['email'] ) ? $options['email'] : null;
      $this->post['email_customer'] = false;
    }

    if ( isset($options['customer']) ) {
      $this->post['cust_id'] = $options['customer'];
    }

    if ( isset($options['ip']) ) {
      $this->post['customer_ip'] = $options['ip'];
    }
  }

  private function add_duplicate_window() {
    if ( $this->duplicate_window != null ) {
      $this->post['duplicate_window'] = $this->duplicate_window;
    }
  }


  /* ARB */

  private function recurring_commit($action, $parameters=array()) {
    $url = $this->is_test() ? self::ARB_TEST_URL : self::ARB_LIVE_URL;

    $headers = array("Content-type: text/xml");

    $data = $this->ssl_post($url, $this->arb_post_data($action, $parameters), array('headers'=>$headers));

    $response = $this->arb_parse($data);

    $message = $this->arb_message_from($response);

    $test_mode = $this->is_test();

    return new Merchant_Billing_Response(
            $this->arb_success_from($response),
            $message,
            $response,
            array(
              'test' => $test_mode,
              'authorization' => $response['subscription_id'],
            )
    );
  }

  private function arb_parse($body) {

    $response = array();

    /*
     * SimpleXML returns some warnings about arb namespace, althought it parse
     * the xml correctly.
      $xml = simplexml_load_string($body);
      $response['ref_id'] = (string) $xml->refId;
      $response['result_code'] = (string) $xml->messages->resultCode;
      $response['code'] = (string) $xml->messages->message->code;
      $response['text'] = (string) $xml->messages->message->text;
      $response['subscription_id'] = (string) $xml->subscriptionId;
     */

    /*
     * Used parsing method from authorize.net example
     */
    $response['ref_id'] = $this->substring_between($body,'<refId>','</refId>');
    $response['result_code'] = $this->substring_between($body,'<resultCode>','</resultCode>');
    $response['code'] = $this->substring_between($body,'<code>','</code>');
    $response['text'] = $this->substring_between($body,'<text>','</text>');
    $response['subscription_id'] = $this->substring_between($body,'<subscriptionId>','</subscriptionId>');

    return $response;
  }

  private function arb_message_from($response) {
    return $response['text'];
  }

  private function arb_success_from($response) {
    return $response['result_code'] == 'Ok';
  }

  private function arb_add_creditcard(Merchant_Billing_CreditCard $creditcard) {
    $expiration_date = $this->cc_format($creditcard->year, 'four_digits') . "-" .
            $this->cc_format($creditcard->month, 'two_Digits');

    $this->xml .= <<< XML
        <payment>
          <creditCard>
            <cardNumber>{$creditcard->number}</cardNumber>
            <expirationDate>{$expiration_date}</expirationDate>
          </creditCard>
        </payment>
XML;
  }

  private function arb_add_address($address) {
    $this->xml .= <<< XML
        <billTo>
          <firstName>{$address['first_name']}</firstName>
          <lastName>{$address['last_name']}</lastName>
        </billTo>
XML;
  }

  private function arb_add_subscription($amount, $options) {
   $this->xml .= <<< XML
      <name>Subscription of {$options['billing_address']['first_name']} {$options['billing_address']['last_name']}</name>
      <paymentSchedule>
        <interval>
          <length>{$options['length']}</length>
          <unit>{$options['unit']}</unit>
        </interval>
        <startDate>{$options['start_date']}</startDate>
        <totalOccurrences>{$options['occurrences']}</totalOccurrences>
        <trialOccurrences>0</trialOccurrences>
      </paymentSchedule>
      <amount>$amount</amount>
      <trialAmount>0</trialAmount>
XML;
  }

  private function arb_post_data($action) {
    $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
      <{$this->RECURRING_ACTIONS[$action]} xmlns="{$this->AUTHORIZE_NET_ARB_NAMESPACE}">
        <merchantAuthentication>
          <name>{$this->options['login']}</name>
          <transactionKey>{$this->options['password']}</transactionKey>
        </merchantAuthentication>
          {$this->xml}
      </{$this->RECURRING_ACTIONS[$action]}>
XML;

    return $xml;
  }

  /*
   * ARB parsing xml
   */
  private function substring_between($haystack,$start,$end) {
    if (strpos($haystack,$start) === false || strpos($haystack,$end) === false)
    {
      return false;
    }
    else
    {
      $start_position = strpos($haystack,$start)+strlen($start);
      $end_position = strpos($haystack,$end);
      return substr($haystack,$start_position,$end_position-$start_position);
    }
}
}
?>
