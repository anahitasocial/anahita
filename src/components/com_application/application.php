<?php

/**
 *
 * @category   Anahita
 * @package    com_application
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2017 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       https://www.GetAnahita.com
 */

class ComApplication extends KObject implements KServiceInstantiatable
{

    /**
     * Session.
     *
     * @var LibSessions
     */
    protected $_session = null;

    /**
     * Template.
     *
     * @var string
     */
    protected $_template = "";

    /**
     * Application Router.
     *
     * @var ComApplicationRouter
     */
    protected $_router = null;

    /**
  	 * The name of the application
  	 *
  	 * @var		array
  	 */
  	protected $_name = "";

    /**
    * Site settings
    *
    * @var object ComSettingsSetting instance
    *
    */
    protected $_site_settings = null;

    /**
    * Class constructor.
    *
    * @param	integer	A client identifier.
    */
    public function __construct(KConfig $config = null)
    {
        $this->_name = $config->session_name;

        if ($config->session) {
           $this->_session = $this->createSession(get_hash($this->_name));
        }

        parent::__construct($config);

        $this->_site_settings = $this->getService('com:settings.setting');
    }

    /**
     * Initialise the application.
     *
     * @param array $options Initialization options
     */
     protected function _initialize(KConfig $config)
     {
         $config->append(array(
             'session' => false,
             'session_name' => $this->getName()
         ));

         parent::_initialize($config);
    }

    /**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KDatabaseTableInterface
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (! $container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
  	 * Create the user session.
  	 *
  	 * Old sessions are flushed based on the configuration value for the cookie
  	 * lifetime. If an existing session, then the last access time is updated.
  	 * If a new session, a session id is generated and a record is created in
  	 * the #__sessions table.
  	 *
  	 * @access	private
  	 * @param	string	The sessions name.
  	 * @return	object	AnSession on success. May call exit() on database error.
  	 */
  	public function createSession($name)
  	{
        $session = KService::get('com:sessions', array(
            'name' => $name
        ));

        $repository = KService::get('repos:sessions.session');

        //purge guest sessions within 10 minutes expiry time
        $repository->purge(600);

        if ($entity = $repository->find(array('sessionId' => $session->getId()))) {
            $entity->updateTime();
        }

  		return $session;
  	}

    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Get the template.
     *
     * @return string The template name
     */
    public function getTemplate()
    {
        if (empty($this->_template)) {

            if (! KService::get('application.registry')->offsetExists('application-template')) {
                KService::get('application.registry')->offsetSet('application-template', $this->_site_settings->template);
            }

            $template = KService::get('application.registry')->offsetGet('application-template');
            $this->setTemplate(pick($template, 'base'));
        }

        return $this->_template;
    }

    /**
     * Overrides the default template that would be used.
     *
     * @param string $template The template name
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Set the application router.
     *
     * @param mixed $router
     */
    public function setRouter($router)
    {
        $this->_router = $router;
        return $this;
    }

    /**
     * Return a reference to the JRouter object.
     *
     * @return ComApplicationRouter
     */
    public function getRouter($name = null, $options = array())
    {
        if (is_null($this->_router)) {
            $this->_router = KService::get('com:application.router', array(
                'enable_rewrite' => $this->_site_settings->sef_rewrite
            ));
        }

        return $this->_router;
    }

    /**
  	 * Method to get the application name
  	 *
  	 * The dispatcher name by default parsed using the classname, or it can be set
  	 * by passing a $config['name'] in the class constructor
  	 *
  	 * @access	public
  	 * @return	string The name of the dispatcher
  	 */
  	function getName()
  	{
  		$name = $this->_name;

  		if (empty($name)) {
  			$r = null;
  			if (! preg_match( '/Com(.*)/i', get_class( $this ), $r)) {
                throw new AnErrorException(
                    "Can't get or parse the class name ",
                    KHttpResponse::INTERNAL_SERVER_ERROR
                );
  			}
  			$name = strtolower($r[1]);
  		}

  		return $name;
  	}
}
