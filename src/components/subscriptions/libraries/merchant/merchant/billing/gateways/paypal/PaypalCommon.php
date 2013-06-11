<?php

/**
 * Description of Merchant_Billing_PaypalCommon
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_PaypalCommon extends Merchant_Billing_Gateway{
  const TEST_URL = 'https://api-3t.sandbox.paypal.com/nvp';
  const LIVE_URL = 'https://api-3t.paypal.com/nvp';

  const FAILURE = 'Failure';
  const PENDING = 'Pending';

  private $SUCCESS_CODES = array('Success', 'SuccessWithWarning');
  const FRAUD_REVIEW_CODE = "11610";

  /**
   * Parse the raw data response from gateway
   *
   * @param string $body
   */
  private function parse($body) {
    parse_str( $body, $response_array );
    if ( $response_array['ACK'] == self::FAILURE ) {
      $error_message = "Error code (". $response_array['L_ERRORCODE0'] . ")\n ".$response_array['L_SHORTMESSAGE0']. ".\n Reason: ".$response_array['L_LONGMESSAGE0'];
      Merchant_Logger::error_log($error_message);
    }
    return $response_array;
  }

  /**
   *
   * @param string $action
   * @return Response
   */
  protected function commit($action) {
    $url = $this->is_test() ? self::TEST_URL : self::LIVE_URL;
    
    $response = $this->parse( $this->ssl_post($url, $this->post_data($action)) );
    
    $options  = array();
    $options['test'] = $this->is_test();
    $options['authorization'] = $this->authorization_from($response);
    $options['fraud_review'] = $this->fraud_review_from($response);
    $options['avs_result'] = $this->avs_result_from($response);
    $options['cvv_result'] = isset($response['CVV2CODE']) ? $response['CVV2CODE'] : null;

    return $this->build_response( $this->success_from($response),
            $this->message_from($response), $response, $options);
  }

  /**
   * Returns success flag from gateway response
   *
   * @param array $response
   * @return string
   */
  private function success_from($response) {
    return ( in_array($response['ACK'], $this->SUCCESS_CODES) );
  }

  /**
   * Returns message (error explanation  or success) from gateway response
   *
   * @param array $response
   * @return string
   */
  private function message_from($response) {
    return ( isset($response['L_LONGMESSAGE0']) ? $response['L_LONGMESSAGE0'] : $response['ACK'] );
  }


  /**
   * Returns fraud review from gateway response
   *
   * @param array $response
   * @return boolean
   */
  private function fraud_review_from($response) {
    if ( isset($response['L_ERRORCODE0']) )
      return ($response['L_ERRORCODE0'] == self::FRAUD_REVIEW_CODE);
    return false;
  }

  /**
   *
   * Returns avs result from gateway response
   *
   * @param array $response
   * @return array
   */
  private function avs_result_from($response) {
    return array( 'code' => isset($response['AVSCODE']) ? $response['AVSCODE'] : null );
  }

  /**
   *
   * @param array $response
   * @return boolean
   */
  private function authorization_from($response) {
    if ( isset($response['TRANSACTIONID']) ) return $response['TRANSACTIONID'];
    if ( isset($response['AUTHORIZATIONID']) ) return $response['AUTHORIZATIONID'];
    if ( isset($response['REFUNDTRANSACTIONID']) ) return $response['REFUNDTRANSACTIONID'];
    return false;
  }
  
  
  
  public function post(array $options)
  {
  	$this->post = array_merge($this->post, $options);
  	return $this;
  }  
}
?>
