<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Theme
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * A Dispatcher controller that dispatches the main theme
 *
 * @category   Anahita
 * @package    Lib_Theme
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class LibThemeDispatcherAbstract extends LibBaseControllerAbstract
{
    /**
     * The Dispatcher view
     * 
     * @var LibThemeViewHtml
     */
    protected $_view;
    
    /**
     * Template paramaeters
     * 
     * @var KConfig
     */
    protected $_params;
    
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
        
        $this->_view = $config->view;
        
        //set the request template
        $this->template = $config->template;
        
        JFactory::getLanguage()->load( $config->language );                     
        
        $this->_params = $config->params;               
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
        $config->append(array(
            'language'  => 'tpl_'.$this->getIdentifier()->package,
            'view'      => 'html'
        ));
        
        $params = '';
        
        $identifier = clone $this->getIdentifier();
        $identifier->path = array();
                
        if (is_readable( dirname($identifier->filepath).DS.'params.ini' ) ) {
            $params = file_get_contents(dirname($identifier->filepath).DS.'params.ini');
        }
        
        $params = new JParameter($params);
        
        $config->append(array(
            'params' => $params->toArray()
        ));
        
        parent::_initialize($config);
    }
            
    /**
     * Renders the theme
     * 
     * @return string
     */
    protected function _actionRender(KCommandContext $context)
    {
        $data     = $context->data;
        $document = $data->document;
                
        //if ( $document instanceof 
        return $this->getView()->content($this->content)->document($document)->display();
    }
    
    /**
     * Set the Dispatcher view
     * 
     * @param mixed $view The Dispatcher main view
     * 
     * @return LibThemeDispatcherDefault
     */
    public function setView($view)
    {
        if ( !$view instanceof LibBaseViewAbstract ) 
        {
            $identifier = $view;
            
            if ( strpos($identifier,'.') === false )  {       
                $identifier = clone $this->getIdentifier();
                $identifier->name = $view;
                $identifier->path = array('view');
            }
            
            $view = $this->getIdentifier( $identifier );
        }
        
        $this->_view = $view;
        
        return $this;
    }
    
    /**
     * Return the viewer
     * 
     * @return LibBaseViewTemplate
     */
    public function getView()
    {
        if ( !$this->_view instanceof LibBaseViewAbstract )
        {
             //Make sure we have a view identifier
            if(!($this->_view instanceof KServiceIdentifier)) {
                $this->setView($this->_view);
            }
            
            $config = array(
                'base_url'  => KRequest::base(),
                'media_url' => 'base://media',
                'params'    => $this->getParams()                
            );

            $this->_view = $this->getService($this->_view, $config);
            
            if ( $this->template ) {
                $this->_view->setLayout( $this->template );   
            }
        }
        
        return $this->_view;
    }
    
    /**
     * Template parameters
     * 
     * @return KConfig
     */
    public function getParams()
    {
        return $this->_params;
    }
}