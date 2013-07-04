<?php
/**
 * Description of Response
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_Response {

  private   $success;
  private   $message;
  protected $params;
  private   $test;
  private   $authorization;
  private   $avs_result;
  private   $cvv_result;
  private   $fraud_review;

  /**
   *
   * @param boolean $success
   * @param string $message
   * @param array $params
   * @param array $options
   */
  public function __construct($success, $message, $params = array(), $options = array() ) {
    $this->success = $success;
    $this->message = $message;
    $this->params  = $params;

    $this->test          = isset($options['test']) ? $options['test'] : false;
    $this->authorization = isset($options['authorization']) ? $options['authorization'] : null;
    $this->fraud_review  = isset($options['fraud_review']) ? $options['fraud_review'] : null;
    $this->avs_result    = isset($options['avs_result']) ? new Merchant_Billing_AvsResult($options['avs_result']) : null;
    $this->cvv_result    = isset($options['cvv_result']) ? new Merchant_Billing_CvvResult($options['cvv_result']) : null;
  }

  public function  __get($name) {
    return isset($this->params[$name]) ? $this->params[$name] : null;
  }

  /**
   *
   * @return boolean
   */
  public function success(){
    return $this->success;
  }

  /**
   *
   * @return boolean
   */
  public function test(){
    return $this->test;
  }

  public function fraud_review(){
    return $this->fraud_review;
  }

  public function authorization(){
    return $this->authorization;
  }

  public function message(){
    return $this->message;
  }

  public function params(){
    return $this->params;
  }
  
  public function avs_result(){
    return $this->avs_result;
  }
  
  public function cvv_result(){
    return $this->cvv_result;
  }

}
