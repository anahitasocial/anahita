<?php

/**
 * Description of Merchant_Billing_HsbcSecureEpayments
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Billing_HsbcSecureEpayments extends Merchant_Billing_Gateway {
  const TEST_URL = 'https://www.secure-epayments.apixml.hsbc.com';
  const LIVE_URL = 'https://www.secure-epayments.apixml.hsbc.com';

  private $CARD_TYPE_MAPPINGS = array(
      'visa' => 1, 'master' => 2, 'american_express' => 8, 'solo' => 9,
      'switch' => 10, 'maestro' => 14
  );

  private $COUNTRY_CODE_MAPPINGS = array(
      'CA' => 124, 'GB' => 826, 'US' => 840
  );

  private $HSBC_CVV_RESPONSE_MAPPINGS = array(
      '0' => 'X',
      '1' => 'M',
      '2' => 'N',
      '3' => 'P',
      '4' => 'S',
      '5' => 'X',
      '6' => 'I',
      '7' => 'U'
  );

  private $TRANSACTION_STATUS_MAPPINGS = array(
      'accepted' => "A",
      'declined' => "D",
      'fraud' => "F",
      'error' => "E",
      'void' => "V",
      'reserved' => "U"
  );

  const APPROVED = 1;
  const DECLINED = 50;
  const DECLINED_FRAUDULENT = 500;
  const DECLINED_FRAUDULENT_VOIDED = 501;
  const DECLINED_FRAUDULENT_REVIEW = 502;
  const CVV_FAILURE = 1055;
  
  private $FRAUDULENT = array(
      'DECLINED_FRAUDULENT',
      'DECLINED_FRAUDULENT_VOIDED',
      'DECLINED_FRAUDULENT_REVIEW', 'CVV_FAILURE');
  
  private $options = array();
  private $xml;
  private $payment_mode = "Y"; #Test mode
  private $payment_mech_type = "CreditCard";

  protected $default_currency = 'EUR';
  protected $supported_countries = array('US', 'GB');
  protected $supported_cardtypes = array('visa', 'master', 'american_express', 'switch', 'solo', 'maestro');
  protected $homepage_url = 'http://www.hsbc.co.uk/1/2/business/cards-payments/secure-epayments';
  protected $display_name = 'HSBC Secure ePayments';
  protected $money_format = 'cents';

  public function __construct($options = array()) {
    $this->required_options('login, password, client_id', $options);

    if (isset($options['currency']))
      $this->default_currency = $options['currency'];

    $this->options = $options;

    $mode = $this->mode();
    if ($mode == 'live')
      $this->payment_mode = 'P';#Production mode
  }

  public function authorize($amount, Merchant_Billing_CreditCard $creditcard, $options = array()) {
    $this->build_xml($amount, $creditcard, 'PreAuth', $options);
    return $this->commit(__FUNCTION__);
  }

  public function purchase($amount, Merchant_Billing_CreditCard $creditcard, $options = array()) {
    $this->build_xml($amount, $creditcard, 'Auth', $options);
    return $this->commit(__FUNCTION__);
  }

  public function capture($amount, $authorization, $options = array()) {
    $options = array_merge($options, array('authorization', $authorization));
    $this->build_xml($amount, $creditcard, 'PostAuth', $options);
  }

  public function void($identification, $options = array()) {
    $this->build_xml($amount, $creditcard, 'Void', $options);
  }

  private function build_xml($amount, Merchant_Billing_CreditCard $creditcard, $type, $options=array()) {
    $this->start_xml();
    $this->insert_data($amount, $creditcard, $type, $options);
    $this->end_xml();
  }

  private function insert_data($amount, Merchant_Billing_CreditCard $creditcard, $type, $options=array()) {
    $month = $this->cc_format($creditcard->month, 'two_digits');
    $year = $this->cc_format($creditcard->year, 'two_digits');

    $this->xml .= <<<XML
          <OrderFormDoc>
            <Mode DataType="String">{$this->payment_mode}</Mode>
            <Consumer>
              <PaymentMech>
                <Type DataType="String">{$this->payment_mech_type}</Type>
                <CreditCard>
                  <Number DataType="String">{$creditcard->number}</Number>
                  <Expires DataType="ExpirationDate">{$month}/{$year}</Expires>
                  <Cvv2Val DataType="String">{$creditcard->verification_value}</Cvv2Val>
                  <Cvv2Indicator DataType="String">1</Cvv2Indicator>
                </CreditCard>
              </PaymentMech>
            </Consumer>
XML;
    $this->add_transaction_element($amount, $type, $options);
    $this->add_billing_address($options);
    $this->add_shipping_address($options);
  }

  private function add_transaction_element($amount, $type, $options) {
    if ($type == 'PreAuth' || $type == 'Auth') {
      $this->xml .= <<<XML
      <Transaction>
        <Type DataType="String">{$type}</Type>
        <CurrentTotals>
          <Totals>
            <Total DataType="Money" Currency="{$this->currency_lookup($this->default_currency)}">{$amount}</Total>
          </Totals>
        </CurrentTotals>
      </Transaction>
XML;
    } elseif ($type == 'PostAuth' || $type == 'Void') {
      $this->xml .= <<<XML
      <Transaction>
        <Type DataType="String">{$type}</Type>
        <Id DataType="String">{$options['authorization']}</Id>
        <CurrentTotals>
          <Totals>
            <Total DataType="Money" Currency="{$this->CURRENCY_MAPPINGS[$this->currency]}">{$amount}</Total>
          </Totals>
        </CurrentTotals>
      </Transaction>
XML;
    }
  }

  private function add_billing_address($options) {
    if (isset($options['billing_address'])) {
      $this->xml .= <<<XML
        <BillTo> 
          <Location>
            <Email DataType="String">{$options['email']}</Email>
XML;
      $this->add_address($options['billing_address']);
      $this->xml .= <<<XML
            <TelVoice DataType="String">{$options['billing_address']['phone']}</TelVoice>
          </Location>
        </BillTo>
XML;
    }
  }

  private function add_shipping_address($options) {
    if (isset($options['shipping_address'])) {
      $this->xml .= <<<XML
        <ShipTo>
          <Location>
            <Email DataType="String">{$options['email']}</Email>
XML;
      $this->add_address($options['shipping_address']);
      $this->xml .= <<<XML
            <TelVoice DataType="String">{$options['shipping_address']['phone']}</TelVoice>
          </Location>
        </ShipTo>
XML;
    }
  }

  private function add_address($options) {
    $this->xml .= <<<XML
      <Address>
        <Name DataType="String">{$options['name']}</Name>
        <Company DataType="String">{$options['company']}</Company>
        <Street1 DataType="String">{$options['address1']}</Street1>
        <Street2 DataType="String">{$options['address2']}</Street2>
        <City DataType="String" >{$options['city']}</City>
        <StateProv DataType="String" >{$options['state']}</StateProv>
        <Country DataType="String">{$this->COUNTRY_CODE_MAPPINGS[$options['country']]}</Country>
        <PostalCode DataType="String">{$options['zip']}</PostalCode>
      </Addresss>
XML;
  }

  private function start_xml() {
    $this->xml = <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
      <EngineDocList>
        <DocVersion DataType="String">1.0</DocVersion>
        <EngineDoc>
          <ContentType DataType="String">OrderFormDoc</ContentType>
          <User>
            <Alias DataType="String">{$this->options['client_id']}</Alias>
            <Name DataType="String">{$this->options['login']}</Name>
            <Password DataType="String">{$this->options['password']}</Password>
          </User>
          <Instructions>
            <Pipeline DataType="String">Payment</Pipeline>
          </Instructions>
XML;
  }

  private function end_xml() {
    $this->xml .= <<<XML
          </OrderFormDoc>
        </EngineDoc>
      </EngineDocList>
XML;
  }

  private function commit($action) {
    $url = $this->is_test() ? self::TEST_URL : self::LIVE_URL;
    $response = $this->parse($this->ssl_post($url, $this->xml));

    return new Merchant_Billing_Response($this->success_from($action, $response), $this->message_from($response), $response, $this->options_from($response));
  }

  private function parse($response_xml) {
    $xml = simplexml_load_string($response_xml);

    $response = array();

    $messages = $xml->EngineDoc->MessageList;
    $overview = $xml->EngineDoc->Overview;
    $transaction = $xml->EngineDoc->OrderFormDoc->Transaction;

    /**
     * Parse messages
     */
    if (!empty($messages)) {

      if (isset($messages->MaxSev))
        $response['severity'] = (string) $messages->MaxSev;

      if (count($messages->Message) == 2) {
        $message = $messages->Message[1];
      } else {
        $message = $messages->Message;
      }

      if (isset($message->AdvisedAction))
        $response['advised_action'] = (string) $message->AdvisedAction;

      if (isset($message->Text))
        $response['error_message'] = (string) $message->Text;
    }
    /**
     * Parse overview
     */
    if (!empty($overview)) {

      if (isset($overview->CcErrCode))
        $response['return_code'] = (string) $overview->CcErrCode;

      if (isset($overview->CcReturnMsg))
        $response['return_message'] = (string) $overview->CcReturnMsg;

      if (isset($overview->TransactionId))
        $response['transaction_id'] = (string) $overview->TransactionId;

      if (isset($overview->AuthCode))
        $response['auth_code'] = (string) $overview->AuthCode;

      if (isset($overview->TransactionStatus))
        $response['transaction_status'] = (string) $overview->TransactionStatus;

      if (isset($overview->Mode))
        $response['mode'] = (string) $overview->Mode;
    }

    /**
     * Parse transaction
     */
    if (!empty($transaction->CardProcResp)) {

      if (isset($transaction->CardProcResp->AvsRespCode))
        $response['avs_code'] = (string) $transaction->CardProcResp->AvsRespCode;

      if (isset($transaction->CardProcResp->AvsDisplay))
        $response['avs_display'] = (string) $transaction->CardProcResp->AvsDisplay;

      if (isset($transaction->CardProcResp->Cvv2Resp))
        $response['cvv2_resp'] = (string) $transaction->CardProcResp->Cvv2Resp;
    }

    return $response;
  }

  private function options_from($response) {
    $options = array();
    $options['authorization'] = $response['transaction_id'];
    $options['test'] = empty($response['mode']) || $response['mode'] != 'P';
    $options['fraud_review'] = in_array($response['return_code'], $this->FRAUDULENT);
    if (!empty($response['cvv2_resp']))
      $options['cvv_result'] = $this->HSBC_CVV_RESPONSE_MAPPINGS[$response['cvv2_resp']];
    $options['avs_result'] = $this->avs_code_from($response);
  }

  private function success_from($action, $response) {
    if ($action == 'authorize' || $action == 'purchase' || $action == 'capture') {
      $transaction_status = $this->TRANSACTION_STATUS_MAPPINGS['accepted'];
    } elseif ($action == 'void') {
      $transaction_status = $this->TRANSACTION_STATUS_MAPPINGS['void'];
    } else {
      $transaction_status = null;
    }

    return ($response['return_code'] == self::APPROVED &&
    $response['transaction_id'] != null &&
    $response['auth_code'] != null &&
    $response['transaction_status'] == $transaction_status);
  }

  private function message_from($response) {
    return (isset($response['return_message']) ? $response['return_message'] : $response['error_message']);
  }

  private function avs_code_from($response) {
    if (empty($response['avs_display']))
      return array('code' => 'U');
    switch ($response['avs_display']) {
      case 'YY':
        $code = "Y";
        break;
      case 'YN':
        $code = "A";
        break;
      case 'NY':
        $code = "W";
        break;
      case 'NN':
        $code = "C";
        break;
      case 'FF':
        $code = "G";
        break;
      default:
        $code = "R";
        break;
    }

    return array('code' => $code);
  }

}
?>
