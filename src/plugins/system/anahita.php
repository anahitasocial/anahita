<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('joomla.plugin.plugin');

require_once JPATH_PLUGINS.'/system/koowa.php';

/**
 * Anahita System Plugin
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgSystemAnahita extends JPlugin 
{
	/**
	 * Constructor
	 * 
	 * @param mixed $subject Dispatcher
	 * @param array $config  Array of configuration
     * 
     * @return void
	 */
	public function __construct($subject, $config = array())
	{	    
        // Command line fixes for Joomla
        if (PHP_SAPI === 'cli') 
        {
            if (!isset($_SERVER['HTTP_HOST'])) {
                $_SERVER['HTTP_HOST'] = '';
            }
            
            if (!isset($_SERVER['REQUEST_METHOD'])) {
                $_SERVER['REQUEST_METHOD'] = '';
            }
        }
        
        if (!function_exists('mysqli_connect')) 
        {
            JFactory::getApplication()->redirect(JURI::base().'templates/system/error_mysqli.html');
        } 
        
        // Check for suhosin
        if(in_array('suhosin', get_loaded_extensions()))
        {
            //Attempt setting the whitelist value
            @ini_set('suhosin.executor.include.whitelist', 'tmpl://, file://');

            //Checking if the whitelist is ok
            if(!@ini_get('suhosin.executor.include.whitelist') || strpos(@ini_get('suhosin.executor.include.whitelist'), 'tmpl://') === false)
            {
                JFactory::getApplication()->redirect(JURI::base().'templates/system/error_suhosin.html');
                return;
            }
        }
        
        //Safety Extender compatibility
        if(extension_loaded('safeex') && strpos('tmpl', ini_get('safeex.url_include_proto_whitelist')) === false)
        {
            $whitelist = ini_get('safeex.url_include_proto_whitelist');
            $whitelist = (strlen($whitelist) ? $whitelist . ',' : '') . 'tmpl';
            ini_set('safeex.url_include_proto_whitelist', $whitelist);
        }
        
        //Set constants
        define('KDEBUG'      , JDEBUG);
        
        //Set path definitions
        define('JPATH_FILES' , JPATH_ROOT);
        define('JPATH_IMAGES', JPATH_ROOT.DS.'images');
        
        //Set exception handler
        set_exception_handler(array(new AnExceptionHandler(), 'exceptionHandler'));
        
        // Koowa : setup
        require_once( JPATH_LIBRARIES.'/anahita/anahita.php');
        
        //instantiate anahita
        Anahita::getInstance(array(           
            'cache_prefix'    => md5(JFactory::getApplication()->getCfg('secret')).'-cache-koowa',
            'cache_enabled'   => JFactory::getApplication()->getCfg('caching')
        ));
        
        //Setup the request
        KRequest::root(str_replace('/'.JFactory::getApplication()->getName(), '', KRequest::base()));                

        if ( !JFactory::getApplication()->getCfg('caching') ) 
        {
            //clear apc cache for module and components
            //@NOTE If apc is shared across multiple services
            //this causes the caceh to be cleared for all of them
            //since all of them starts with the same prefix. Needs to be fix
            clean_apc_with_prefix('cache_mod');
            clean_apc_with_prefix('cache_com');
            clean_apc_with_prefix('cache_plg');
            clean_apc_with_prefix('cache_system');
            clean_apc_with_prefix('cache__system');
        }
        
		KService::get('plg:storage.default');
		
        if ( JDEBUG && JFactory::getApplication()->isAdmin() )
        {
            JError::raiseNotice('','Anahita is running in the debug mode. Please make sure to turn the debug off for production.');
        }
        
        JFactory::getLanguage()->load('overwrite',   JPATH_ROOT);
		JFactory::getLanguage()->load('lib_anahita', JPATH_ROOT);
        
        parent::__construct($subject, $config);
	}
		
	/**
	 * onAfterInitialise handler
	 *
	 * Adds the mtupgrade folder to the list of directories to search for JHTML helpers.
	 * 
	 * @return null
	 */
	public function onAfterRoute()
	{
		$type 	= strtolower(KRequest::get('request.format', 'cmd', 'html'));
				
		$format = $type;
		
		if ( KRequest::type() == 'AJAX' ) {
		    $format = strtolower(KRequest::get('server.HTTP_X_REQUEST', 'cmd', 'raw'));
		}
		
		$document =& JFactory::getDocument();
		
		//if a document type is raw then convert it to HTML
		//and set the format to html
		if ( $format == 'raw' )
		{
		    $document = JDocument::getInstance('html');
		
		    //set the format to html
		    $format = 'html';
		    //set the tmpl to raw
		    JRequest::setVar('tmpl', 		'raw');
		}
		
		KRequest::set('get.format',		$format);
				
		//wrap a HTML document around a decorator
		if (JFactory::getDocument()->getType() == 'html')
		{
		    if ( $format == 'html' )
		    {
		        //set the document
		        $document = JFactory::getApplication()->isAdmin() ? 
		            JDocument::getInstance('html') : 
		            new AnDocumentDecorator($document); 
		    }		        
		    else {
		        $document = JDocument::getInstance('raw');
		    }
		}

        //set the error document to a decorated
        //document object
        if ( !JFactory::getApplication()->isAdmin() )
        {
            //set the error document
            $error =& JDocument::getInstance('error');
            $error = new AnDocumentDecorator($error);
        }
        
		$tag   = JFactory::getLanguage()->getTag();
		
		if ( JFactory::getApplication()->isAdmin() )
		{
		    JHTML::script('lib_koowa/js/koowa.js', 	   'media/');
		    JHTML::script('lib_anahita/js/anahita.js?lang='.$tag.'&token='.JUtility::getToken(), 'media/');
		    JHTML::script('lib_anahita/js/admin.js',   'media/');
		}
		else
		{
		    //JHTML::script('lib_anahita/js/min/bootstrap.js', 'media/');
		    //JHTML::script('lib_anahita/js/anahita.js?lang='.$tag.'&token='.JUtility::getToken().'&'.uniqid(), 'media/');
		    //JHTML::script('lib_anahita/js/site.js?'.uniqid(), 'media/');
		}
		
		if ( !JFactory::getApplication()->isAdmin() ) 
		{
    		KService::get('com://site/default.filter.string');
		}	    	
	}
	
	/**
	 * store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		global $mainframe;

		if( !$succes )
			return false;
        
        $person =   KService::get('repos://site/people.person')
                    ->getQuery()
                    ->disableChain()
                    ->userId($user['id'])
                    ->fetch();
                    ;
							
		if ( $person ) 
		{		    
			KService::get('com://site/people.helper.person')->synchronizeWithUser($person, JFactory::getUser($user['id']) );
			
		} else 
		{
			$person = KService::get('com://site/people.helper.person')->createFromUser( JFactory::getUser($user['id']) );
		}
		
		$person->save();
		
		return true;
	}	
    
	/**
	 * store user method
	 *
	 * Method is called before user data is deleted from the database
	 *
	 * @param 	array		holds the user data
	 */
	public function onBeforeDeleteUser($user)
	{							
		$person = 	KService::get('repos://site/people.person')
                    ->getQuery()
                    ->disableChain()
                    ->userId($user['id'])
					->fetch();
					;
		
		if(!$person)
			return;

		$apps = KService::get('repos://site/apps.app')->getQuery()->disableChain()->fetchSet();
		
		foreach($apps as $app) 
		{
		    KService::get('anahita:event.dispatcher')->addEventSubscriber($app->getDelegate());
		}
		
		$person->destroy();
	}
}

/**
 * Anahita Exception Handler
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnExceptionHandler
{
    /**
     * Thrown exception
     * 
     * @var Exception
     */
    protected $_exception;
    
    /**
     * Catch all exception handler
     *
     * Calls the Joomla error handler to process the exception
     *
     * @param object an Exception object
     * @return void
     */
    public function exceptionHandler($exception)
    {
        $this->_exception = $exception; //store the exception for later use
        
        //Change the Joomla error handler to our own local handler and call it
        JError::setErrorHandling( E_ERROR, 'callback', array($this,'errorHandler'));
        
        //Make sure we have a valid status code
        JError::raiseError(KHttpResponse::isError($exception->getCode()) ? $exception->getCode() : 500, $exception->getMessage());
    }

    /**
     * Custom JError callback
     *
     * Push the exception call stack in the JException returned through the call back
     * adn then rener the custom error page.
     *
     * @param object A JException object
     * @return void
     */
    public function errorHandler($error)
    {
        $error->setProperties(array(
            'backtrace' => $this->_exception->getTrace(),
            'file'      => $this->_exception->getFile(),
            'line'      => $this->_exception->getLine()
        ));
        
        if(JFactory::getConfig()->getValue('config.debug')) {
            $error->set('message', (string) $this->_exception);
        } else {
            $error->set('message', KHttpResponse::getMessage($error->code));
        }
        
        //Make sure the buffers are cleared
        while(@ob_get_clean());
        
        //Throw json formatted error
        if( KRequest::format() == 'json' || KRequest::type() == 'AJAX' )
        {
            $properties = array(
                'message' => $error->message,
                'code'    => $error->code
            );
            
            if(KDEBUG)
            {
                $properties['data'] = array(
                    'file'      => $error->file,
                    'line'      => $error->line,
                    'function'  => $error->function,
                    'class'     => $error->class,
                    'args'      => $error->args,
                    'info'      => $error->info
                );
            }
            
            //Encode data
            $data = json_encode(array(
                'version'  => '1.0', 
                'errors' => array($properties)
            ));
            
            JResponse::setHeader('Content-Type','application/json');
            JResponse::setBody($data);
            
            echo JResponse::toString();
            JFactory::getApplication()->close(0);
        }
        else {
            JError::customErrorPage($error);
        }
        
    }
}

/**
 * Document Decorator. Decorates the render method and uses the TmplAbstract class 
 * to render the template
 * 
 * @category   Anahita
 * @package    Plugins
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDocumentDecorator
{
    /**
     * Document
     * 
     * @var JDocumentHTML
     */
    protected $_document;
    
    /**
     * Document Object
     *
     * @param JDocumentHTML $document Document object
     * 
     * @return void
     */
    public function __construct($document)
    {
        $this->_document = $document;
    }
    
    /**
     * Overrwrites the JDocumentHtml::render()
     *
     * @access public
     * @param boolean 	$cache		If true, cache the output
     * @param array		$params		Associative array of attributes
     * @return 	The rendered data
     */
    function render( $caching = false, $params = array())
    {   
        $params     = new KConfig($params);
        
        $params->append(array(
            'file' => $this->_document instanceof JDocumentError ? 'error.php' : 'index.php'
        )); 
		jimport('joomla.filesystem.file');
        
        $tmpl  = JFile::stripExt(JFile::getName($params['file']));        
        
        if ( $tmpl == 'index' ) {
            $tmpl = 'default';    
        }
                
        $data['filename'] = $params['file'];
        
        $document         = $this->_document;
        
        $identifier = 'tmpl://site/'.$params['template'].'.dispatcher';
        
        $dispatcher   = KService::get($identifier, array('template'=>$tmpl));
        
        if ( $this->_document instanceof JDocumentError )
        {
            JResponse::setHeader('status', $this->_document->_error->code.' '.str_replace( "\n", ' ', $this->_document->_error->message ));
            
            $error = $this->_document->_error;
            
            if ( !isset($error) ) {
                $error = JError::raiseWarning( 403, JText::_('ALERTNOTAUTH') );    
            }
            
            $this->_document->_error = $error;
            
        }       
        else {
            
            $item    = JMenu::getInstance('site')->getActive();
            $option  = KRequest::get('get.option', 'cmd');
            $view    = KRequest::get('get.view', 'cmd');
            
            if ( ($item && $item->alias == 'home') || ($option == 'com_content' && $view == 'frontpage') ) {
                $dispatcher->content(null);
            }
            
            elseif ( isset($this->_document->_buffer) ) {
                $dispatcher->content(implode('', $this->_document->_buffer['component']));
            }
        }
        
        return $dispatcher->render(array('document'=>$this->_document));                
    }
    
    /**
     * Forwards __get to the docuemnt
     *
     * @param string $key
     * 
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_document->$key;
    }
    
    /**
     * Forwards the call to the JDocuemntHTML object
     *
     * @param string $method    The called method
     * @param array  $arguments Array of arguments
     * 
     * @return mixed Return the JDocumentHtml::$method($arguments)
     */
    public function __call($method, $arguments)
    {
        return call_object_method($this->_document, $method, $arguments);
    }
}