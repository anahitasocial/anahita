<?php
/**
 * Description of Merchant_Billing_Eurobank
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_Eurobank extends Merchant_Billing_Gateway {

  const TEST_URL = 'https://eptest.eurocommerce.gr/proxypay/apacsonline';
  const LIVE_URL = 'https://ep.eurocommerce.gr/proxypay/apacsonline';

  private $options = array();
  private $xml;
  protected  $default_currency  = 'EUR';
  protected  $supported_countries = array('GR');
  protected  $supported_cardtypes = array('visa', 'master');
  protected  $homepage_url = 'http://www.eurobank.gr/online/home/generic.aspx?id=79&mid=635';
  protected  $display_name = 'Eurobank Euro-Commerce';
  protected  $money_format = 'cents';


  /**
   *
   * @param array $options
   * Options
   * 'login'    - Your merchan id.         (REQUIRED)
   * 'password' - Your encrypted password. (REQUIRED)
   */
  public function __construct($options) {
    $this->required_options('login, password', $options);

    if ( isset( $options['currency'] ) )
      $this->default_currency = $options['currency'];

    $this->options = $options;
  }

  /**
   *
   * @param number                      $money      - Total order amount.       (REQUIRED)
   * @param Merchant_Billing_CreditCard $creditcard - A creditcard class object (REQUIRED)
   * @param array                       $options
   *
   * @return Merchant_Billing_Response
   */
  public function authorize($money, Merchant_Billing_CreditCard $creditcard, $options=array()) {
    $this->required_options('customer_email', $options);
    $this->build_xml($money, 'PreAuth', $creditcard, $options);
    return $this->commit();
  }

  /**
   *
   * @param number $money
   * @param string $authorization
   * @param array  $options
   *
   * @return Merchant_Billing_Response
   */
  public function capture($money, $authorization, $options=array()) {
    $options = array_merge($options, array('authorization'=>$authorization));
    $this->build_xml($money, 'Capture', null, $options);
    return $this->commit();
  }

  /**
   *
   * @param number $money
   * @param string $identification
   * @param array  $options
   *
   * @return Merchant_Billing_Response
   */
  public function credit($money, $identification, $options=array()) {
    $options = array_merge($options, array('authorization'=>$identification));
    $this->build_xml($money, 'Refund', null, $options);
    return $this->commit();
  }

  /**
   *
   * @param string $identification
   * @param array  $options
   *
   * @return Merchant_Billing_Response
   */
  public function void($identification, $options = array()) {
    $options = array_merge($options, array('authorization'=>$identification));
    $this->build_xml(0, 'Cancel', null, $options);
    return $this->commit();
  }

  /**
   *
   * @return Merchant_Billing_Response
   */
  private function commit() {
    $url = $this->is_test() ? self::TEST_URL : self::LIVE_URL;

    $post_data = 'APACScommand=NewRequest&data='.trim($this->xml);
    $response = $this->parse($this->ssl_post($url, $post_data));

    /*
     * Sample of response
      <?xml version="1.0" encoding="UTF-8"?>
      <RESPONSE>
        <ERRORCODE>0</ERRORCODE>
        <ERRORMESSAGE>0</ERRORMESSAGE>
        <REFERENCE>or5342-CD</REFERENCE>
        <PROXYPAYREF>34543</PROXYPAYREF>
        <SEQUENCE>4562</SEQUENCE>
      </RESPONSE>
    */

    return new Merchant_Billing_Response($this->success_from($response), $this->message_from($response), $response, $this->options_from($response));
  }

  /**
   *
   * @param string $response_xml
   *
   * @return array
   */
  private function parse($response_xml) {
    $xml = simplexml_load_string($response_xml);
    $response = array();

    $response['error_code']   = (string) $xml->ERRORCODE;
    $response['message']      = (string) $xml->ERRORMESSAGE;
    $response['reference']    = (string) $xml->REFERENCE;
    $response['proxypay_ref'] = (string) $xml->PROXYPAYREF;
    $response['sequence']     = (string) $xml->SEQUENCE;

    return $response;
  }

  private function success_from($response) {
    return $response['error_code'] == '0';
  }

  /**
   *
   * @param string $response
   *
   * @return boolean
   */
  private function message_from($response) {
    return $response['message'];
  }

  /**
   *
   * @param string $response
   *
   * @return array
   */
  private function options_from($response) {
    $options = array();
    $options['test']          = $this->is_test();
    $options['authorization'] = $response['reference'];
    $options['proxypay_ref']  = $response['proxypay_ref'];
    $options['sequence']      = $response['sequence'];

    return $options;
  }

  /**
   *
   * @param Merchant_Billing_CreditCard $creditcard
   */
  private function build_payment_info(Merchant_Billing_CreditCard $creditcard) {
    $month = $this->cc_format($creditcard->month, 'two_digits');
    $year  = $this->cc_format($creditcard->year, 'two_digits');

    $this->xml .= <<<XML
      <PaymentInfo>
        <CCN>{$creditcard->number}</CCN>
        <Expdate>{$month}{$year}</Expdate>
        <CVCCVV>{$creditcard->verification_value}</CVCCVV>
        <InstallmentOffset>0</InstallmentOffset>
        <InstallmentPeriod>0</InstallmentPeriod>
      </PaymentInfo>
XML;
  }


  /**
   *
   * @param number                      $money
   * @param string                      $type
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array                       $options
   */
  private function build_xml($money, $type, Merchant_Billing_CreditCard $creditcard = null, $options=array()) {
    $merchant_desc = isset($options['merchant_desc']) ? $options['merchant_desc'] : null;
    $merchant_ref = isset($options['authorization']) ? $options['authorization'] : "REF " . date("YmdH:i:s", time());
    $customer_email = isset($options['customer_email']) ? $options['customer_email'] : "";

    $this->xml = <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
      <JProxyPayLink>
        <Message>
          <Type>{$type}</Type>
          <Authentication>
            <MerchantID>{$this->options['login']}</MerchantID>
            <Password>{$this->options['password']}</Password>
          </Authentication>
          <OrderInfo>
            <Amount>{$this->amount($money)}</Amount>
            <MerchantRef>{$merchant_ref}</MerchantRef>
            <MerchantDesc>{$merchant_desc}</MerchantDesc>
            <Currency>{$this->currency_lookup($this->default_currency)}</Currency>
            <CustomerEmail>{$customer_email}</CustomerEmail>
            <Var1 />
            <Var2 />
            <Var3 />
            <Var4 />
            <Var5 />
            <Var6 />
            <Var7 />
            <Var8 />
            <Var9 />
          </OrderInfo>
XML;
    if ( $creditcard != null ) $this->build_payment_info($creditcard);
    $this->xml .= <<<XML
        </Message>
      </JProxyPayLink>
XML;
  }
}
?>
