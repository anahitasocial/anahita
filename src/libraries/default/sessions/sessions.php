<?php

class LibSessions extends KObject implements KServiceInstantiatable
{

	/**
	* state contants
	*/
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
	protected $_storage = null;

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
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_unset();
			session_destroy();
		}

		parent::__construct($config);

		//set default sessios save handler
		ini_set('session.save_handler', 'files');

		//disable transparent sid support
		ini_set('session.use_trans_sid', '0');

		$this->_storage = $this->getService('com:sessions.storage.'.$config->storage);

		if (isset($config->name)) {
			session_name(get_hash($config->name));
		}

		if (isset($config->id)) {
			session_id($config->id);
		}

		$this->_state =	$config->state;
		$this->_expire = $config->expire;
		$this->_security = explode(',', $config->security);
		$this->_force_ssl = $config->force_ssl;
		$this->_namespace = $config->namespace;

		ini_set('session.gc_maxlifetime', $this->getExpire());

		$this->_setCookieParams();
		$this->_start();
		$this->_setCounter();
		$this->_setTimers();
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
			'expire' => LibSessionsDomainEntitySession::MAX_LIFETIME + time(),
			'security' => array('fix_browser'),
			'force_ssl' => is_ssl(),
			'namespace' => '__anahita',
			'storage' => 'database'
		));

		parent::_initialize($config);
	}

	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfigInterface object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KDatabaseTableInterface
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
		if (! $container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

		$session = $container->get($config->service_identifier);

		if ($session->getState() === self::STATE_EXPIRED) {
			$session->restart();
		}

        return $session;
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
	* Start a session
	*
	* Creates a session (or resumes the current one based on the state of the session)
 	*
	* @access protected
	* @return boolean $result true on success
	*/
	private function _start()
	{
		if ($this->_state === self::STATE_RESTART) {
			session_id($this->_createId());
		}

		session_cache_limiter('none');

		return session_start();
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

		if (is_null($token) || $forceNew) {
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
	 */
	public function hasToken($tokenCheck, $forceExpire = true)
	{
		$stored = $this->get('session.token');

		if ($stored !== $tokenCheck) {

			if ($forceExpire) {
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
			throw new LibSessionsException("Can't obtain the session name!\n");
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
			throw new LibSessionsException("Can't obtain the session id!\n");
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
			dirname(__FILE__).DS.'storage',
			SCANDIR_SORT_DESCENDING
		);

		$handlers = preg_grep('/^([^.])/', $handlers);

		$exclude = array(
			'abstract.php',
			'exception.php',
			'interface.php'
		);

		$handlers = array_diff($handlers, $exclude);

		$names = array();

		foreach ($handlers as $handler) {
			if (strpos($handler, '.php')) {
				$name = substr($handler, 0, strrpos($handler, '.'));
				if ($this->getService('com:sessions.storage.'.$name)->test()){
					$names[] = $name;
				}
			}
		}

		return $names;
	}

	public function getStorage()
	{
		return $this->_storage;
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
   public function get($property = null, $default = null, $namespace = '')
   {
	   if($this->_state !== self::STATE_ACTIVE && $this->_state !== self::STATE_EXPIRED) {
		   throw new LibSessionsException("Session does not exist!\n");
	   }

	   $namespace = empty($namespace) ? $this->_namespace : $namespace;

	   if (isset($_SESSION[$namespace][$property])) {
		   return $_SESSION[$namespace][$property];
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
	public function set($name, $value = null, $namespace = '')
	{
		if ($this->_state !== self::STATE_ACTIVE) {
			throw new LibSessionsException("Session isn't active!\n");
		}

		$namespace = empty($namespace) ? $this->_namespace : $namespace;

		if (is_null($value)) {
			unset($_SESSION[$namespace][$name]);
		} else {
			$_SESSION[$namespace][$name] = $value;
		}

		return $this;
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
			throw new LibSessionsException("Session isn't active!\n");
		}

		return isset($_SESSION[$this->_namespace][$name]);
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
		if ($this->_state !== self::STATE_ACTIVE) {
			throw new LibSessionsException("Session isn't active!\n");
		}

		$value = null;

		if (isset($_SESSION[$this->_namespace][$name])) {
			$value = $_SESSION[$this->_namespace][$name];
			unset($_SESSION[$this->_namespace][$name]);
		}

		return $value;
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
	 */
	public function destroy()
	{
		// session was already destroyed
		if ($this->_state === self::STATE_DESTROYED) {
			return;
		}

		// In order to kill the session altogether, like to log the user out, the session id
		// must also be unset. If a cookie is used to propagate the session id (default behavior),
		// then the session cookie must be deleted.
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - $this->_expire, '/');
		}

		session_unset();
		session_destroy();

		$this->_state = self::STATE_DESTROYED;
	}

	/**
    * restart an expired or locked session
	*
	* @access public
	* @return boolean $result true on success
	*/
	public function restart()
	{
		$this->destroy();

		if ($this->_state !==  self::STATE_DESTROYED) {
			throw new LibSessionsException("Session is not destroyed!\n");
		}

		$this->_storage->register();

		$this->_state = self::STATE_RESTART;

		$id	= $this->_createToken(strlen($this->getId()));

		session_id($id);

		$this->_start();

		$this->_state = self::STATE_ACTIVE;

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
			throw new LibSessionsException("Session isn't active!\n");
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
	protected function _createId($length = 32)
	{
		$id = '';

		while (strlen($id) < $length)  {
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

	   $cookie = session_get_cookie_params();
	   $cookie['secure'] = is_ssl();

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
		$max = strlen($chars) - 1;
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
		if (! $this->has('session.timer.start')) {
			$start = time();
			$this->set('session.timer.start', $start);
			$this->set('session.timer.last', $start);
			$this->set('session.timer.now', $start);
		}

		$this->set('session.timer.last', $this->get('session.timer.now'));
		$this->set('session.timer.now', time());

		return true;
	}
}
