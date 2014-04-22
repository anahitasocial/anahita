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
 * JSite application. Temporary until merged with the dispatcher
 *
 * @category   Anahita
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class JSite extends JApplication
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
     * @var JRouter
     */
    protected $_router;
    
    /** 
     * Constructor.
     *
     * @param array $config An optional KConfig object with configuration options.
     * 
     * @return void
     */
    public function __construct($config = array())
    {
        $config['clientId'] = 0;
        parent::__construct($config);
    }

    /**
    * Initialise the application.
    *
    * @param array $options Initialization options
    * 
    * @return void
    */
    public function initialise( $options = array())
    {
        // if a language was specified it has priority
        // otherwise use user or default language settings
        if (empty($options['language']))
        {
            $user = & JFactory::getUser();
            $lang   = $user->getParam( 'language' );

            // Make sure that the user's language exists
            if ( $lang && JLanguage::exists($lang) ) {
                $options['language'] = $lang;
            } else {
                $params =  JComponentHelper::getParams('com_languages');
                $client =& JApplicationHelper::getClientInfo($this->getClientId());
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
    * Login authentication function
    *
    * @param array $credentials Array( 'username' => string, 'password' => string )
    * @param array $options     Array( 'remember' => boolean )
    * 
    * @see JApplication::login
    */
    public function login($credentials, $options = array())
    {
         //Set the application login entry point
         if(!array_key_exists('entry_url', $options)) {
             $options['entry_url'] = JURI::base().'index.php?option=com_people&view=session&action=login';
         }

        return parent::login($credentials, $options);
    }

    /**
     * Get the appliaction parameters
     *
     * @param   string  The component option
     * @return  object  The parameters object
     * @since   1.5
     */
    public function &getParams($option = null)
    {
        static $params = array();
        $hash = '__default';
        if(!empty($option)) $hash = $option;
        if (!isset($params[$hash]))
        {
            // Get component parameters
            if (!$option) {
                $option = JRequest::getCmd('option');
            }
            $params[$hash] =& JComponentHelper::getParams($option);

            // Get menu parameters
            $menus  =& JSite::getMenu();
            $menu   = $menus->getActive();

            $title       = htmlspecialchars_decode($this->getCfg('sitename' ));
            $description = $this->getCfg('MetaDesc');

            // Lets cascade the parameters if we have menu item parameters
            if (is_object($menu))
            {
                $params[$hash]->merge(new JParameter($menu->params));
                $title = $menu->name;

            }

            $params[$hash]->def( 'page_title'      , $title );
            $params[$hash]->def( 'page_description', $description );
        }

        return $params[$hash];
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
        	if ( !KService::get('application.registry')
        		->offsetExists('application-template') )
        	{
        		//get the template
        		$template = KService::get('repos://site/templates.menu', array(
        				'resources'         => 'templates_menu',
        				'identity_property' => 'menuid'
        		))->getQuery()->clientId(0)->fetchValue('template');

        		KService::get('application.registry')
        		->offsetSet('application-template', $template);
        	}
        	
        	$template = KService::get('application.registry')->offsetGet('application-template');
            
            $this->setTemplate(pick($template, 'base'));
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
     * Return a reference to the JPathway object.
     *
     * @return JMenu
     */
    public static function &getMenu($name = null, $options = array())
    {
        $menu =& parent::getMenu('site', $options);
        return $menu;
    }

    /**
     * Return a reference to the JPathway object
     * 
     * @return JPathway
     */
    public function &getPathway($name = null, $options = array())
    {
        $options = array();
        $pathway =& parent::getPathway('site', $options);
        return $pathway;
    }

    /**
     * Set the application router
     * 
     * @param mixed $router
     * 
     * @return void
     */
    public function setRouter($router)
    {
        $this->_router = $router;
        return $this;
    }
    
    /**
     * Return a reference to the JRouter object.
     *
     * @return  JRouter
     */
    function &getRouter($name = null, $options = array())
    {
        if ( !isset($this->_router) )
        {
            $this->_router = KService::get('com://site/application.router', array('enable_rewrite'=>JFactory::getConfig()->getValue('sef_rewrite')));
        }
        
        return $this->_router;
    }
}
