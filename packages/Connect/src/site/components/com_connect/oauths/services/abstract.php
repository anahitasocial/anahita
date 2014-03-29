<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth_Service
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

require_once(dirname(__FILE__).'/../core.php');

/**
 * Authenticate agains an oauth service
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth_Service
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComConnectOauthServiceAbstract extends KObject
{	
	/**
	 * Request Token URL
	 * 
	 * @var string
	 */	
	public $api_url;
		
	/**
	 * Request Token URL
	 * 
	 * @var string
	 */	
	public $request_token_url;

	/**
	 * Access Token URL
	 * 
	 * @var string
	 */		
	public $access_token_url;
	
	/**
	 * Authenticate URL
	 * 
	 * @var string
	 */		
	public $authenticate_url;

	/**
	 * Authorize URL
	 * 
	 * @var string
	 */		
	public $authorize_url;
	
    /**
     * Check if the service is enabled
     * 
     * @var boolean
     */
    protected $_enabled;
    
	/**
	 * Consumer object
	 * 
	 * @var AnOauthConsumer
	 */
	protected $_consumer;			

	/**
	 * Token
	 * 
	 * @var OAuthToken
	 */
	protected $_token;
	
	/**
	 * OAtuh Version
	 * 
	 * @return string
	 */
	protected $_version;
	
	/**
	 * API response format
	 * 
	 * @var string
	 */
	protected $_response_format;
	
    /**
     * A Flag that determines if an API is readyonly or not
     * 
     * @var booelan
     */
    protected $_readonly;
    
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{				
		parent::__construct($config);
		
		$this->api_url  		 = $config->api_url;
		$this->request_token_url = $config->request_token_url;
		$this->access_token_url  = $config->access_token_url;		
		$this->authenticate_url  = $config->authenticate_url;
		$this->authorize_url	 = $config->authorize_url;
		$this->_version			 = $config->version;
		
        $this->_enabled  = $config->enabled;
        
        $this->_readonly = $config->readonly;
        
		$this->setConsumer($config->consumer);	
		$this->setToken( $config->token );							
		$this->_response_format   = $config->response_format;
	}

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(   
		    'readonly'          => false,		                 
            'enabled'           => true,
			'token'				=> null,
			'version'			=> '1.0',
			'scope'				=> array(),
			'consumer'			=> new ComConnectOauthConsumer(new KConfig()),
			'response_format' 	=> 'json',
			'api_url'			=> '' ,
			'request_token_url' => '' ,
			'access_token_url'  => '' ,
			'authenticate_url'  => '' ,
			'authorize_url'		=> ''
		));
	
		parent::_initialize($config);
	}	
	
    /**
     * Return if the API is readonly or it allow writes
     * 
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readonly;
    }
    
	/**
	* Post an status update to the service provider for the logge-in user  
	* 
	* @return array
	*/
	abstract public function postUpdate($message);
	
    /**
    * Return whether an actor can add the service or not
    * 
    * @param ComActorsDomainEntityActor $actor The actor that wants to add the service
    * 
    * @return boolean
    */
    abstract public function canAddService($actor);
        
 	/**
	* Return a user object representing the logged-in user 
	* 
	* @return ComConnectOauthUser
	*/
	public function getUser()
    {
        if ( !isset($this->_user) )
        {
            $this->_user = new ComConnectOauthUser();
            $data        = $this->_getUserData();
            if ( $data )
                foreach($data as $key => $value) {
                    $this->_user->$key = $value;   
                }                   
        }
        
        return $this->_user;
    }
    
    /**
     * Return the user data
     * 
     * @return array
     */
	abstract protected function _getUserData();
    
	/**
	 * Return the service provider oauth version
	 * 
	 * @return string
	 */
	public function getVersion()
	{
		return $this->_version;
	}
	
    /**
     * Returns if the service is enabled
     * 
     * @return boolean
     */
    public function enabled()
    {
        return $this->_enabled;
    }
    
	/**
	 * Return service name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->getIdentifier()->name;
	}
			
	/**
	 * Get the consumer
	 * 
	 * @return ComConnectOauthConsumer
	 */
	public function getConsumer()
	{
		return $this->_consumer;
	}
		
	/**
	 * Set the consumer
	 * 
	 * @param  ComConnectOauthConsumer $consumer
	 * @return AnOauthAdapterAbstract
	 */
	public function setConsumer($consumer)
	{
		//never let hte consumer to be null
		$this->_consumer = pick($consumer, new ComConnectOauthConsumer(new KConfig()));
		return $this;
	}		
	
	/**
	 * Returns an OAuthToken token
	 * 
	 * @return OAuthToken
	 */
	public function getToken()
	{
		return $this->_token;
	}
	
	/**
	 * Set the token
	 * 
	 * @param  array|string $key
	 * @param  string $secret  
	 * @return string
	 */
	public function setToken($key, $secret = null)
	{
		$key = KConfig::unbox($key);
		
		if ( is_array($key) ) {
			extract($key, EXTR_OVERWRITE);
		}
		
		$this->_token = null;
		
		if ( $key )
			$this->_token = new OAuthToken($key, $secret);
								
		return $this;	
	}
	
	/**
	 * Return the authorize URL
	 * 
	 * @param  array $data Query to pass to the authorization URL
	 * @return string
	 */
	public function getAuthorizationURL($data = array())
	{
		if ( version_compare($this->getVersion(), '1.0', '=') ) {
			$response = $this->requestRequestToken();
			$data['oauth_token'] = $response->oauth_token;
		}
		
		$data = KConfig::unbox($data);
		
		foreach($data as $key => $value) {
			if ( is_array($value) ) {
				$data[$key] = implode(',', $value);
			}
		}
		
		$data = http_build_query($data);
		
		if ( !empty($data) )
			$data = '?'.$data;
			
		return $this->authorize_url.$data;
	}
	
	/**
	 * Request for a request token.
	 * 
	 * @param  array $data 
	 * @return string
	 */
	public function requestRequestToken($data = array())
	{
		$config = new KConfig(array('data'=>$data));
		
		$config->append(array(
			'url'	  => $this->request_token_url	,					
			'data'	  => array('oauth_callback'=>$this->getConsumer()->callback_url)
		));
				
		$request = $this->getRequest($config);
		$response  = $request->send();

		if ( $response->successful() ) {
			$result   = $response->parseQuery();			
			$_SESSION['oauth_token_secret'] = $result->oauth_token_secret;	
		}
		else
			$result = '';
				
		return $result;
	}
	
	/**
	 * Get the access token using an authorized request token
	 * 
	 * @param  KConfig|array $data 
	 * @return string
	 */
	public function requestAccessToken($data)
	{
		$secret = isset($_SESSION['oauth_token_secret']) ? $_SESSION['oauth_token_secret'] : null;
		
		$data 	= new KConfig($data);
		
		$this->setToken($data->oauth_token, $secret);
		
		$result = $this->getRequest(array(
			'url'	=> $this->access_token_url,
			'data'	=> array('oauth_verifier' => $data->oauth_verifier)
		))->send()->parseQUery();
		
		$this->setToken($result->oauth_token, $result->oauth_token_secret);
		
		unset($_SESSION['oauth_token_secret']);
		
		return $this;
	}
	
	/**
	 * Creates an returns a request
	 * 
	 * @param  array $config
	 * @return ComConnectOauthRequest
	 */
	public function getRequest($config = array())
	{
		$config['consumer'] = $this->getConsumer();
		$config['token']	= $this->getToken();
		$config['version']	= $this->getVersion();
					
		return new ComConnectOauthRequest(new KConfig($config));
	}
	
	/**
	 * Make a GET request
	 *
	 * @param  string resource name
	 * @param  array $data
	 * @return mixed
	 */
	public function get($resource=null, $data=array())
	{
		return $this->call($resource, KHttpRequest::GET, $data);
	}
	
	/**
	 * Make a POST request
	 *
	 * @param  array $data
	 * @return mixed
	 */
	public function post($resource, $data=array())
	{
		return $this->call($resource, KHttpRequest::POST, $data);
	}
	
	/**
	 * Make a DELETE request
	 *
	 * @param  array $data
	 * @return mixed
	 */
	public function delete($resource, $data=array())
	{
		return $this->call($resource, KHttpRequest::DELETE, $data);		
	}

	/**
	 * Make a PUT request
	 *
	 * @param  array $data
	 * @return mixed
	 */
	public function put($resource, $data=array())
	{
		return $this->call($resource, KHttpRequest::PUT, $data);
	}	

	/**
	 * Make a PUT request
	 *
	 * @param  array $data
	 * @return mixed
	 */
	public function call($resource, $method, $data=array())
	{
		$resource = $this->api_url.'/'.$resource;
		$response = $this->getRequest(array('url'=>$resource, 'method'=>$method, 'data'=>$data))->send();
        $result   = null;
        if ( strlen($response) ) {
            $result   = $response->{'parse'.$this->_response_format}();    
        }
		return $result;
	}
}
?>