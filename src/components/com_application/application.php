<?php

/**
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

class ComApplication extends KObject
{
    /**
     * Template.
     *
     * @var string
     */
    protected $_template;

    /**
     * Application Router.
     *
     * @var JRouter
     */
    protected $_router;

    /**
  	 * The name of the application
  	 *
  	 * @var		array
  	 * @access	protected
  	 */
  	var $_name = null;

    /**
    * Class constructor.
    *
    * @param	integer	A client identifier.
    */
    public function __construct($config = array())
    {
        //set the application name
        $this->_name = $this->getName();

        //Set the session default name
        if(!isset($config['session_name'])) {
           $config['session_name'] = $this->_name;
        }

        //Enable sessions by default
        if(!isset($config['session'])) {
          $config['session'] = true;
        }

        //Set the default configuration file
        if(!isset($config['config_file'])) {
          $config['config_file'] = 'configuration.php';
        }

        //create the configuration object
        $this->_createConfiguration(JPATH_CONFIGURATION.DS.$config['config_file']);

        //create the session if a session name is passed
        if($config['session'] !== false) {
          $this->createSession(JUtility::getHash($config['session_name']));
        }
    }

    /**
     * Initialise the application.
     *
     * @param array $options Initialization options
     */
     protected function _initialize(KConfig $config)
     {
         parent::_initialize($config);

         $settings = new JConfig();
         $config->language = $settings->language;

         // One last check to make sure we have something
         if (!JLanguage::exists($config->language)) {
            $config->language = 'en-GB';
         }
    }

    /**
     * Create the configuration registry
     *
     * @access	private
     * @param	string	$file 	The path to the configuration file
     * return	JConfig
     */
    protected function &_createConfiguration($file)
    {
      jimport( 'joomla.registry.registry' );

      require_once($file);

      $config = new JConfig();
      $registry =& JFactory::getConfig();
      $registry->loadObject($config);

      return $config;
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

        $config = new KConfig(array(
            'name' => $name
        ));

        $session = KService::get('com:sessions', array('config' => $config));

        $storage = $session->getStorage();
        $storage->gc($session->getExpire());

        if ($storage->read($session->getId())) {
            $storage->update($session->getId());
            return $session;
        }

  		//Session doesn't exist yet, initalise and store it in the session table
  		$session->set('registry', new JRegistry('session'));

        $person = KService::get('repos:people.person')
                    ->getEntity()->setData(array(
                        'usertype' => ComPeopleDomainEntityPerson::USERTYPE_GUEST
                    ))->reset();

        $session->set('person', (object) $person->getData());

        if (!$storage->write($session->getId(), '')) {
            throw new KException("Coudn't write in the session table");
            return;
  		}

  		return $session;
  	}

    /**
   	 * Gets a configuration value.
   	 *
   	 * @access	public
   	 * @param	string	The name of the value to get.
   	 * @return	mixed	The user state.
   	 * @example	application/japplication-getcfg.php Getting a configuration value
   	 */
   	public function getSystemSetting( $name )
   	{
        $setting = new JConfig();

        if(isset($setting->$name)){
          return $setting->$name;
        }

        return null;
   	}

    /**
     * Get the template.
     *
     * @return string The template name
     */
    public function getTemplate()
    {
        if (!isset($this->_template)) {
            if (!KService::get('application.registry')->offsetExists('application-template')) {

                $settings = new JConfig();
                $template = (isset($settings->template)) ? $settings->template : 'shiraz';

                KService::get('application.registry')->offsetSet('application-template', $template);
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
     * @return JRouter
     */
    public function &getRouter($name = null, $options = array())
    {
        if (!isset($this->_router)) {
            $settings = new JConfig();
            $this->_router = KService::get('com:application.router', array(
                'enable_rewrite' => $settings->sef_rewrite
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

  		if (empty( $name )) {

  			$r = null;

  			if (!preg_match( '/Com(.*)/i', get_class( $this ), $r)) {
            throw new AnErrorException(
              "Can't get or parse the class name.",
              KHttpResponse::INTERNAL_SERVER_ERROR
            );
  			}

  			$name = strtolower( $r[1] );
  		}

  		return $name;
  	}
}
