<?php

jimport('joomla.error.log');

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

require_once(JPATH_LIBRARIES.'/merchant/merchant.php');

/**
 * Paypal Gateway
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainPaymentGatewayPaypal extends KObject implements ComSubscriptionsDomainPaymentGatewayInterface
{   
    /**
     * Gateway config
     * 
     * @var array
     */ 
    protected $_gateway_config;
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);                
        
        //activate the test mode
        if ( $config->test_mode ) {
            Merchant_Billing_Base::mode('test');
        }
        
        $this->_gateway_config = $config->toArray();
    }
        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $params = get_config_value('com_subscriptions');
        
        $config->append(array(
                'test_mode' => $params->get('test_mode', false),
                'login' 	=> $params->get('login'),
                'password'	=> $params->get('password'),
                'signature' => $params->get('signature')
        ));
    
        parent::_initialize($config);
    }

    /**
     * Return a payment method 
     * 
     * @param string $token   The authorization token
     * @param string $country If the country is known then it will set the country code 
     * 
     * @return ComSubscriptionsDomainPaymentMethodToken
     */
    public function getExpressPaymentMethod($token, &$country = null)
    {
        $gateway  = new Merchant_Billing_PaypalExpress($this->_gateway_config);
        $response = $gateway->get_details_for($token, '');
        
        $method   = new ComSubscriptionsDomainPaymentMethodToken('Paypal', 
                array('payer_id'=>$response->payer_id(),'token'=>$token));
        
        $country = $response->COUNTRYCODE;
        
        return $method; 
    }
    
    /**
     * Get the authorization URL
     * 
     * @param ComSubscriptionsDomainPaymentPayload $payload
     * @param string $return_url The return url
     * @param string $cancel_url The cancel url
     * 
     * @return string
     */
    public function getAuthorizationURL(ComSubscriptionsDomainPaymentPayload $payload, 
            $return_url, 
            $cancel_url)
    {

        $gateway = new Merchant_Billing_PaypalExpress($this->_gateway_config);
        
        $options  = array(
                'return_url'	 	 	=> (string)$return_url,
                'cancel_return_url'	 	=> (string)$cancel_url,
                'NOSHIPPING'			=> 1,
                'LANDINGPAGE' 			=> 'Billing'
        );
        
        if($payload->getRecurring())
        {
            $options['billing_type'] = 'RecurringPayments';
            $options['billing_agreement_description'] = $payload->description;
        }
        
        $response = $gateway->setup_purchase($payload->getTotalAmount(), $options);
        
        if ( $response->success() )
            return $gateway->url_for_token($response->TOKEN);
        else {
            throw new KException($response->message());
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see ComSubscriptionsDomainPaymentGatewayAbstract::process()
     */
    public function process(ComSubscriptionsDomainPaymentPayload $payload)
    {   
        $options = new KConfig();
        
        $options->append(array(                
            'description' => $payload->description,
        ));
        
        $args = array($payload->getTotalAmount());
        
        if ( $payload->payment_method instanceof ComSubscriptionsDomainPaymentMethodToken ) 
        {
            $gateway = new Merchant_Billing_PaypalExpress($this->_gateway_config);
            $options->append($payload->payment_method->options);
        }
        else 
        {
            $gateway = new Merchant_Billing_Paypal($this->_gateway_config);
            $ip = KRequest::get('server.REMOTE_ADDR', 'raw');            
            if ( !$this->getService('koowa:filter.ip')->validate($ip) || strlen($ip) <= 7  ) {
                $ip = '127.0.0.1';
            }            
            $contact = $payload->payment_method->address;            
            $options->append(array(
                    'order_id'    => $payload->order_id,                    
                    'ip'		  => $ip,
                    'address' => array(
                            'address1' => $contact->address,
                            'zip' 	 => $contact->zip,
                            'state' 	 => $contact->state,
                            'city'     => $contact->city,
                            'country'  => $contact->country
                    )
            ));  

            $args[] = $payload->payment_method->creditcard;
        }
        
        $method = 'purchase';
        
        if( $payload->getRecurring() )
        {
            $method = 'recurring';                        
            $options['occurrences'] = $payload->getRecurring()->frequency;
            $options['unit']        = $payload->getRecurring()->unit;
            $options['start_date']  = $payload->getRecurring()->start_date;            
        }
        
        $args[] = KConfig::unbox($options);
        
        $gateway->post(array(
                'TAXAMT'  => $payload->tax_amount,
                'ITEMAMT' => $payload->amount                
        ));
        
        $response = call_object_method($gateway, $method, $args);
        $result   = $response->success();
        
        if ( !$result ) {
            $this->_logError($response);            
        }
        return $result;           
    }
    
    /**
     * Logs an error
     * 
     * @param mixed $response
     * 
     * @return void
     */
    protected function _logError($response)
    {
        $log      = JLog::getInstance('system_log.php');
        $message  = "\nerror_message=".$response->message()."\n";        
        if ( $response->cvv_result() )
            $message .= "cvv_result=".implode(" ",$response->cvv_result()->toArray())."\n";

        if ( $response->avs_result() )
            $message .= "avs_result=".implode(" ",$response->avs_result()->toArray())."\n";
        
        $log->addEntry(array('comment'=>$message, 'level'=>'ERROR'));
    }
}