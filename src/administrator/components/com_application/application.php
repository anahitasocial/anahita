<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * JAdministrator application. Temporary until merged with the dispatcher
 *
 * @category   Anahita
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class JAdministrator extends JApplication
{
    /**
     * Template
     * 
     * @var string
     */
    protected $_template;
    
    /**
     * Application Router
     *
     * @var 
     */    
    protected $_router;
        
	/**
	* Class constructor
	*
	* @access protected
	* @param	array An optional associative array of configuration settings.
	* Recognized key values include 'clientId' (this list is not meant to be comprehensive).
	*/
	function __construct($config = array())
	{
		$config['clientId'] = 1;
		parent::__construct($config);

		//Set the root in the URI based on the application name
		JURI::root(null, str_replace('/'.$this->getName(), '', JURI::base(true)));
	}

	/**
	* Initialise the application.
	*
	* @access public
	* @param array An optional associative array of configuration settings.
	*/
	function initialise($options = array())
	{
		// if a language was specified it has priority
		// otherwise use user or default language settings
		if (empty($options['language']))
		{
			$user = & JFactory::getUser();
			$lang	= $user->getParam( 'admin_language' );

			// Make sure that the user's language exists
			if ( $lang && JLanguage::exists($lang) ) {
				$options['language'] = $lang;
			} else {
				$params = JComponentHelper::getParams('com_languages');
				$client	=& JApplicationHelper::getClientInfo($this->getClientId());
				$options['language'] = $params->get($client->name, 'en-GB');
			}
		}

		// One last check to make sure we have something
		if ( ! JLanguage::exists($options['language']) ) {
			$options['language'] = 'en-GB';
		}

		parent::initialise($options);
	}

	/**
	* Route the application
	*
	* @access public
	*/
	function route()
	{
		$uri = JURI::getInstance();

		if($this->getCfg('force_ssl') >= 1 && strtolower($uri->getScheme()) != 'https') {
			//forward to https
			$uri->setScheme('https');
			$this->redirect($uri->toString());
		}
	}

	/**
	 * Return a reference to the JRouter object.
	 *
	 * @access	public
	 * @return	JRouter.
	 * @since	1.5
	 */
	function &getRouter($name = NULL, $options = Array())
	{
	    if ( !isset($this->_router) ) {
	        $router = KService::get('com://admin/application.router');    
	    }
		
		return $router;
	}

	/**
	* Login authentication function
	*
	* @param	array 	Array( 'username' => string, 'password' => string )
	* @param	array 	Array( 'remember' => boolean )
	* @access public
	* @see JApplication::login
	*/
	function login($credentials, $options = array())
	{
		//The minimum group
		$options['group'] = 'Public Backend';

		 //Make sure users are not autoregistered
		$options['autoregister'] = false;

		//Set the application login entry point
		if(!array_key_exists('entry_url', $options)) {
			$options['entry_url'] = JURI::base().'index.php?option=com_user&task=login';
		}

		$result = parent::login($credentials, $options);

		if(!JError::isError($result))
		{
			$lang = JRequest::getCmd('lang');
			$lang = preg_replace( '/[^A-Z-]/i', '', $lang );
			$this->setUserState( 'application.lang', $lang  );			
		}

		return $result;
	}

    /**
     * Get the template
     * 
     * @return string The template name
     */
    public function getTemplate()
    {
        if ( !isset($this->_template) ) 
        {
            //get the template
            $template = KService::get('repos:templates.menu', array(
                    'resources'         => 'templates_menu',
                    'identity_property' => 'menuid'
                ))->getQuery()->clientId(1)->fetchValue('template');
            
            $this->setTemplate(pick($template, 'mission')); 
        }
        
        return $this->_template;
    }

    /**
     * Overrides the default template that would be used
     *
     * @param string $template The template name
     * 
     * @return void
     */
    public function setTemplate( $template )
    {        
        $this->_template = $template;
    }

   /**
	* Deprecated, use JURI::root() instead.
	*
	* @since 1.5
	* @deprecated As of version 1.5
	* @see JURI::root()
	*/
	function getSiteURL()
	{
	   return JURI::root();
	}
}
