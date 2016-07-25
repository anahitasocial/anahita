<?php

class LibSession extends KObject
{
	const STATE_ACTIVE = 'active';
	const STATE_EXPIRED = 'expired';
	const STATE_DESTROYED = 'destroyed';
	const STATE_RESTART = 'restart';
	const STATE_ERROR = 'error';

	/**
	 * internal state
	 *
	 * @access protected
	 * @var	string $_state one of 'active'|'expired'|'destroyed|'error'
	 * @see getState()
	 */
	protected $_state = '';

	/**
	 * Maximum age of unused session
	 *
	 * @access protected
	 * @var	string $_expire minutes
	 */
	protected $_expire = 0;

	/**
	 * The session store object
	 *
	 * @access protected
	 * @var	object A AnStorage object
	 */
	protected $_store = null;

	/**
	* security policy
	*
	* Default values:
	*  - fix_browser
	*  - fix_adress
	*
	* @access protected
	* @var array $_security list of checks that will be done.
	*/
	protected $_security = array();

	/**
	* Force cookies to be SSL only
	*
	* @access protected
	* @default false
	* @var bool $force_ssl
	*/
	protected $_force_ssl = false;

	/**
	*	Session name space
	* 	@var string
	*/
	protected $_namespace = '';

	/**
	*	Singleton instance of LibSession class
	* 	@var LibSession instance
	*/
	private static $_instance = null;

	/**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
		parent::__construct($config);

		if (session_status() === PHP_SESSION_ACTIVE) {
			session_unset();
			session_destroy();
		}

		//set default sessios save handler
		ini_set('session.save_handler', 'files');

		//disable transparent sid support
		ini_set('session.use_trans_sid', '0');

		//create handler
		$settins = new JConfig();
		$this->_store = KService::get('com:session.storage.'.$settins->session_handler);

		if (isset($config->name)) {
			session_name(md5($config->name));
		}

		if (isset($config->id)) {
			session_id(md5($config->name));
		}

		$this->_state =	$config->state;
		$this->_expire = $config->expire;
		$this->_security = explode(',', $config->security);
		$this->_force_ssl = $config->force_ssl;
		$this->_namespace = $config->namespace;

		$this->_setCookieParams();
		$this->_start();
		$this->_setCounter();
		$this->_setTimers();

		// perform security checks
		$this->_validate();
	}

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
		$config->append(array(
			'state' => self::STATE_ACTIVE,
			'expire' => 7200,
			'security' => array('fix_browser'),
			'force_ssl' => isSSl(),
			'namespace' => '__default'
		));

		parent::_initialize($config);
	}

	/**
	 * Session object destructor
	 *
	 * @access protected
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Get current state of session
	 *
	 * @access public
	 * @return string The session state
	 */
    public function getState()
    {
		return $this->_state;
	}

	/**
	 * Get expiration time in minutes
	 *
	 * @access public
	 * @return integer The session expiration time in minutes
	 */
    public function getExpire()
    {
		return $this->_expire;
    }

	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 *
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 *
	 * @param boolean $forceNew If true, force a new token to be created
	 * @access public
	 * @return string The session token
	 */
	public function getToken($forceNew = false)
	{
		$token = $this->get('session.token');

		//create a token
		if ($token === null || $forceNew) {
			$token = $this->_createToken(12);
			$this->set('session.token', $token);
		}

		return $token;
	}

	/**
	 * Method to determine if a token exists in the session. If not the
	 * session will be set to expired
	 *
	 * @param	string	Hashed token to be verified
	 * @param	boolean	If true, expires the session
	 * @since	1.5
	 * @static
	 */
	public function hasToken($tokenCheck, $forceExpire = true)
	{
		$stored = $this->get('session.token');

		if ($stored !== $tokenCheck) {

			if($forceExpire) {
				$this->_state = self::STATE_EXPIRED;
			}

			return false;
		}

		return true;
	}

	/**
	 * Get session name
	 *
	 * @access public
	 * @return string The session name
	 */
	public function getName()
	{
		if ($this->_state === self::STATE_DESTROYED) {
			throw new LibSessionException("Can't obtain the session name!\n");
			return;
		}

		return session_name();
	}

	/**
	 * Get session id
	 *
	 * @access public
	 * @return string The session name
	 */
	public function getId()
	{
		if ($this->_state === self::STATE_DESTROYED) {
			throw new LibSessionException("Can't obtain the session id!\n");
			return;
		}

		return session_id();
	}

	/**
	 * Get the session handlers
	 *
	 * @access public
	 * @return array An array of available session handlers
	 */
	public function getStores()
	{
		$handlers = scandir(
			dirname(__FILE__).DS.'storages',
			SCANDIR_SORT_DESCENDING
		);

		$exclude = array('abstract.php', 'exception.php', 'interface.php');
		$names = array();

		foreach ($handlers as $handler) {
			if (strpos($handler, '.php') && !in_array($handler, $exclude)) {
				$name = substr($handler, 0, strrpos($handler, '.'));
				if ($this->getService('com:people.session.storage.'.$name)->test()){
					$names[] = $name;
				}
			}
		}

		return $names;
	}

	/**
	* Check whether this session is currently created
	*
	* @access public
	* @return boolean $result true on success
	*/
	public function isNew()
	{
		$counter = $this->get('session.counter');
		return (boolean) $counter;
	}

	/**
	* Get data from the session store
	*
	* @access public
	* @param  string $name			Name of a variable
	* @param  mixed  $default 		Default value of a variable if not set
	* @param  string 	$namespace 	Namespace to use, default to 'default'
	* @return mixed  Value of a variable
	*/
   public function &get($property = null, $default = null)
   {
	   if($this->_state !== self::STATE_ACTIVE && $this->_state !== self::STATE_EXPIRED) {
		   throw new LibSessionException("Session does not exist!\n");
		   return;
	   }

	   if (isset($_SESSION[$this->_namespace][$property])) {
		   return $_SESSION[$this->_namespace][$property];
	   }

	   return $default;
   }

	/**
	 * Set data into the session store
	 *
	 * @access public
	 * @param  string $name  		Name of a variable
	 * @param  mixed  $value 		Value of a variable
	 * @return mixed  Old value of a variable
	 */
	public function set($name, $value = null)
	{
		if($this->_state !== self::STATE_ACTIVE) {
			throw new LibSessionException("Session isn't active!\n");
			return;
		}

		$old = isset($_SESSION[$this->_namespace][$name]) ? $_SESSION[$this->_namespace][$name] : null;

		if ($value === null) {
			unset($_SESSION[$this->_namespace][$name]);
		} else {
			$_SESSION[$this->_namespace][$name] = $value;
		}

		return $old;
	}

	/**
	* Check wheter data exists in the session store
	*
	* @access public
	* @param string   $name Name of variable
	* @return boolean $result true if the variable exists
	*/
	public function has($name)
	{

		if($this->_state !== self::STATE_ACTIVE) {
			throw new LibSessionException("Session isn't active!\n");
			return;
		}

		return isset( $_SESSION[$this->_namespace][$name] );
	}

	/**
	* Unset data from the session store
	*
	* @access public
	* @param  string 	$name 		Name of variable
	* @return mixed $value the value from session or NULL if not set
	*/
	public function clear($name)
	{
		if($this->_state !== self::STATE_ACTIVE) {
			throw new LibSessionException("Session isn't active!\n");
			return;
		}

		$value = null;

		if (isset($_SESSION[$this->_namespace][$name])) {
			$value = $_SESSION[$this->_namespace][$name];
			unset($_SESSION[$this->_namespace][$name]);
		}

		return $value;
	}

	/**
	* Start a session
	*
	* Creates a session (or resumes the current one based on the state of the session)
 	*
	* @access protected
	* @return boolean $result true on success
	*/
	private function _start()
	{
		//  start session if not startet
		if( $this->_state === self::STATE_RESTART ) {
			session_id( $this->_createId() );
		}

		session_cache_limiter('none');
		session_start();

		return true;
	}

	/**
	 * Frees all session variables and destroys all data registered to a session
	 *
	 * This method resets the $_SESSION variable and destroys all of the data associated
	 * with the current session in its storage (file or DB). It forces new session to be
	 * started after this method is called. It does not unset the session cookie.
	 *
	 * @static
	 * @access public
	 * @return void
	 * @see	session_unset()
	 * @see	session_destroy()
	 */
	public function destroy()
	{
		// session was already destroyed
		if ($this->_state === self::STATE_DESTROYED) {
			return true;
		}

		// In order to kill the session altogether, like to log the user out, the session id
		// must also be unset. If a cookie is used to propagate the session id (default behavior),
		// then the session cookie must be deleted.
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 42000, '/');
		}

		session_unset();
		session_destroy();

		$this->_state = self::STATE_DESTROYED;

		return true;
	}

	/**
    * restart an expired or locked session
	*
	* @access public
	* @return boolean $result true on success
	* @see destroy
	*/
	public function restart()
	{
		$this->destroy();

		if ($this->_state !==  self::STATE_DESTROYED) {
			throw new LibSessionException("Session is not destroyed!\n");
			return false;
		}

		$this->_store->register();

		$this->_state = self::STATE_RESTART;

		$id	= $this->_createId(strlen($this->getId()));

		session_id($id);

		$this->_start();

		$this->_state = self::STATE_ACTIVE;

		$this->_validate();
		$this->_setCounter();

		return true;
	}

	/**
	* Create a new session and copy variables from the old one
	*
	* @abstract
	* @access public
	* @return boolean $result true on success
	*/
	public function fork()
	{
		if ($this->_state !== self::STATE_ACTIVE) {
			throw new LibSessionException("Session isn't active!\n");
			return false;
		}

		session_regenerate_id();

		return true;
	}

	/**
	* Writes session data and ends session
	*
	* @access public
	* @see	session_write_close()
	*/
	public function close() {
		session_write_close();
	}

	/**
	* Create a session id
	*
	* @static
	* @access protected
	* @return string Session ID
	*/
	protected function _createId( )
	{
		$id = 0;

		while (strlen($id) < 32)  {
			$id .= mt_rand(0, mt_getrandmax());
		}

		$id	= md5(uniqid($id, true));

		return $id;
	}

	/**
	* Set session cookie parameters
	*
	* @access protected
	*/
	protected function _setCookieParams() {

	   $cookie	= session_get_cookie_params();
	   $cookie['secure'] = isSSL();

	   session_set_cookie_params(
		   $cookie['lifetime'],
		   $cookie['path'],
		   $cookie['domain'],
		   $cookie['secure']
	   );
	}

	/**
	* Create a token-string
	*
	* @access protected
	* @param int $length lenght of string
	* @return string $id generated token
	*/
	protected function _createToken($length = 32)
	{
		$chars = '0123456789abcdef';

		$max = strlen( $chars ) - 1;
		$token = '';
		$name = session_name();

		for ($i = 0; $i < $length; ++$i) {
			$token .= $chars[(rand(0, $max))];
		}

		return md5($token.$name);
	}

	/**
	* Set counter of session usage
	*
	* @access protected
	* @return boolean $result true on success
	*/
	protected function _setCounter()
	{
		$counter = $this->get('session.counter', 0);

		$counter++;

		$this->set('session.counter', $counter);

		return true;
	}

	/**
	* Set the session timers
	*
	* @access protected
	* @return boolean $result true on success
	*/
	protected function _setTimers()
	{
		if(!$this->has('session.timer.start'))
		{
			$start = time();

			$this->set('session.timer.start' , $start );
			$this->set('session.timer.last' , $start );
			$this->set('session.timer.now' , $start );
		}

		$this->set('session.timer.last', $this->get('session.timer.now'));
		$this->set('session.timer.now', time());

		return true;
	}

	/**
	* Do some checks for security reason
	*
	* - timeout check (expire)
	* - ip-fixiation
	* - browser-fixiation
	*
	* If one check failed, session data has to be cleaned.
	*
	* @access protected
	* @param boolean $restart reactivate session
	* @return boolean $result true on success
	* @see http://shiflett.org/articles/the-truth-about-sessions
	*/
	protected function _validate($restart = false)
	{

		// allow to restart a session
		if ($restart) {
			$this->_state =	self::STATE_ACTIVE;
			$this->set('session.client.address', null);
			$this->set('session.client.forwarded', null);
			$this->set('session.client.browser', null);
			$this->set('session.token', null);
		}

		// check if session has expired
		if ($this->_expire) {

			$curTime = $this->get('session.timer.now', 0);
			$maxTime = $this->get('session.timer.last', 0) +  $this->_expire;

			// empty session variables
			if ($maxTime < $curTime) {
				$this->_state =	self::STATE_EXPIRED;
				return false;
			}
		}

		// record proxy forwarded for in the session in case we need it later
		if (isset( $_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$this->set('session.client.forwarded', $_SERVER['HTTP_X_FORWARDED_FOR']);
		}

		// check for client adress
		if (in_array('fix_adress', $this->_security) && isset($_SERVER['REMOTE_ADDR'])) {

			$ip	= $this->get( 'session.client.address' );

			if ($ip === null) {
				$this->set('session.client.address', $_SERVER['REMOTE_ADDR']);
			}
			elseif ($_SERVER['REMOTE_ADDR'] !== $ip)
			{
				$this->_state = self::STATE_ERROR;
				return false;
			}
		}

		// check for clients browser
		if(in_array('fix_browser', $this->_security) && isset($_SERVER['HTTP_USER_AGENT']))
		{
			$browser = $this->get('session.client.browser');

			if ($browser === null) {
				$this->set( 'session.client.browser', $_SERVER['HTTP_USER_AGENT']);
			}
		}

		return true;
	}
}
